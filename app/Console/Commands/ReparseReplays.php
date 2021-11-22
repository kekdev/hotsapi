<?php

namespace App\Console\Commands;

use App\Player;
use App\Replay;
use App\Services\ParserService;
use DB;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Storage;

class ReparseReplays extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hotsapi:reparse {min_id=0} {max_id=1000000000}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Redo parsing for replays';

    /**
     * Create a new command instance.
     */
    public function __construct(private ParserService $parser)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $min_id = $this->argument('min_id');
        $max_id = $this->argument('max_id');
        $this->info("Reparsing replays, id from $min_id to $max_id");
        //Replay::where('id', '>=', $min_id)->where('id', '<=', $max_id)->with('players')->chunk(100, function ($x) { return $this->reparse($x); });
        $this->getBrokenReplays()->reverse()->each(fn($x) => $this->reparse([$x]));
    }

    public function getBrokenReplays()
    {
        $replays = collect([
            "No map" => DB::select('SELECT id FROM replays WHERE game_map_id IS NULL'),
            "No hero" => DB::select('SELECT DISTINCT(replay_id) AS id FROM players WHERE hero_id IS NULL'),
            "No players" => DB::select('SELECT r.id AS id FROM replays r LEFT JOIN players p ON p.replay_id = r.id WHERE p.id IS NULL'),
            "Wrong player count" => DB::select('SELECT replay_id AS id, count(*) AS cnt FROM players GROUP BY replay_id HAVING cnt != 10'),
        ]);

        $ids = $replays->flatten(1)->pluck('id')->unique();
        $stats = $replays->map(fn($item, $key) => "$key: " . (is_countable($item) ? count($item) : 0))->implode("\n");
        $this->info("Broken replay summary:\n$stats\nUnique: " . count($ids));
        return Replay::whereIn('id', $ids)->get();
    }

    /**
     * Reparse collection of replays
     *
     * @param $replays
     */
    public function reparse($replays)
    {
        foreach ($replays as $replay) {
            $this->info("Parsing replay id=$replay->id, file=$replay->filename");
            $tmpFile = tempnam('', 'replay_');
            try {
                $content = Storage::cloud()->get("$replay->filename.StormReplay");
                file_put_contents($tmpFile, $content);
                $parseResult = $this->parser->analyze($tmpFile, true);
                if ($parseResult->status != ParserService::STATUS_SUCCESS) {
                    $this->error("Error parsing file id=$replay->id, file=$replay->filename. Status: $parseResult->status");
                    continue;
                }
                $replay->fill($parseResult->data)->save();
                foreach ($parseResult->data['players'] as $playerData) {
                    $player = $replay->players->where('blizz_id', $playerData['blizz_id'])->first();
                    if ($player) {
                        $player->fill($playerData)->save();
                    } else {
                        // apparently `create` doesn't automatically add model to attribute array
                        $replay->players []= $replay->players()->create($playerData);
                    }
                }
                if ((is_countable($replay->players) ? count($replay->players) : 0) != 10) {
                    $this->error("Wrong player count " . (is_countable($replay->players) ? count($replay->players) : 0) . ", replay id=$replay->id, file=$replay->filename");
                }
            } catch (\Exception $e) {
                $this->error("Error parsing file id=$replay->id, file=$replay->filename: $e");
            } finally {
                unlink($tmpFile);
            }
        }
    }
}

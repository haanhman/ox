<?php

namespace App\Console\Commands;

use App\Entities\Youtube;
use App\Repositories\WordRepository;
use Illuminate\Console\Command;

class YConvertCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'y:convert';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert youtube data';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(WordRepository $wordRepository)
    {
        $data = $wordRepository->pluck('video_data');
        $values = [];
        foreach ($data as $item) {
            $videoData = json_decode($item, true);
            foreach ($videoData as $accent => $vd) {
                if (!empty($vd['results'])) {
                    foreach ($vd['results'] as $video) {
                        $values[$video['vid']] = [
                            'youtube_id' => $video['vid'],
                            'cid' => $video['cid'],
                            'id2' => $video['id'],
                            'src' => $video['src'],
                            'accent' => $accent
                        ];
                    }
                }
            }
        }
        $list = array_chunk($values, 500);
        foreach ($list as $rows) {
            $this->insertYoutube($rows);
        }
    }

    private function insertYoutube($values)
    {
        Youtube::insertIgnore($values);
    }
}

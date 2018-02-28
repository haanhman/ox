<?php

namespace App\Console\Commands;

use App\Criteria\FirstRecordCriteria;
use App\Repositories\YoutubeRepository;
use Illuminate\Console\Command;

class YoutubeCaptionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'y:c';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private $youtubeRepository;


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(YoutubeRepository $youtubeRepository)
    {
        parent::__construct();
        $this->youtubeRepository = $youtubeRepository;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->youtubeRepository->pushCriteria(app(FirstRecordCriteria::class));
        $youtube = $this->youtubeRepository->first();
        if (empty($youtube)) {
            $this->error('End data');
            die;
        }

        $youtubeUrl = 'https://www.youtube.com/watch?v=' . $youtube->youtube_id;
        $html = get_web_page($youtubeUrl);
        preg_match('/captionTracks\\\":\[(.*?),\\\/x', $html, $matches);

        if (!isset($matches[1])) {
            $this->error('Not found URL Youtube');
            $this->updateCaption([], $youtube->id);
            $this->refreshCommand();
            die;
        }
        $url = $this->getYoutubeBaseUrl($matches[1] . '}');
        $this->warn(date('d/m/Y H:i:s') . ': ' . $youtube->id);
        $this->info('URL: ' . $youtube->youtube_id);
        $xml = get_web_page($url);
        if(empty($xml)) {
            $this->updateCaption([], $youtube->id);
            $this->refreshCommand();
        }
        $dom = new \SimpleXMLElement($xml);
        $caption = [];
        foreach ($dom[0]->body->p as $item) {
            $row = [];
            foreach ($item->attributes() as $key => $vl) {
                $row[$key] = (int)$vl;
            }
            $row['text'] = trim($item->__toString());
            $caption[] = $row;
        }
        $this->updateCaption($caption, $youtube->id);
        $this->refreshCommand();
    }

    private function refreshCommand($seconds = 1) {
        sleep($seconds);
        $this->call('y:c');
    }

    public function updateCaption($caption, $id) {
        if(empty($caption)) {
            $this->error('Error');
        }
        $this->youtubeRepository->update([
            'subtitle' => empty($caption) ? '1' : json_encode($caption)
        ], $id);
    }

    private function getYoutubeBaseUrl($str, $lang = 'en')
    {

        $str = str_replace('\"', '"', $str);
        $str = str_replace('\\\'', '\'', $str);
        $str = str_replace('\\\"', '\\"', $str);

        $json = json_decode($str, true);
        $baseUrl = $json['baseUrl'];
        $baseUrl = str_replace('\u0026','&', $baseUrl);
        $option = parse_url($baseUrl);
        parse_str($option['query'], $query);
        unset($query['kind']);
        if($query['lang'] != 'en-GB') {
            $query['lang'] = $lang;
        }
        $query['fmt'] = 'srv3';
        $url = 'https://www.youtube.com/api/timedtext?' . http_build_query($query);
        return $url;
    }
}

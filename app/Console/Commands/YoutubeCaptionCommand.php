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

    private $yid = 0;
    private $accent = '';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->youtubeRepository->pushCriteria(app(FirstRecordCriteria::class));
        $youtube = $this->youtubeRepository->get();
        if ($youtube->isEmpty()) {
            die('het roi');
        }


        foreach ($youtube as $item) {
            $sub = collect(json_decode($item->subtitle, true))->where('text', '!=', '');
            $key = array_column($sub->toArray(), 'start');
            $key = array_map(function ($item) {
                return number_format($item, 2, '.', '');
            }, $key);

            $arr = array_combine($key, $sub->toArray());
            ksort($arr);
            $this->youtubeRepository->update([
                'subtitle' => json_encode($arr),
                's1' => 1
            ], $item->id);
        }

        $this->info('Xong');
        return;

        $youtube = $this->youtubeRepository->first();
        if (empty($youtube)) {
            $this->error('End data');
            die;
        }

        $this->yid = $youtube->id;
        $this->accent = $youtube->accent;

        $youtubeUrl = 'https://www.youtube.com/watch?v=' . $youtube->youtube_id;
        $youtubeUrl = 'https://www.youtube.com/watch?v=gs8zmShBUVk';
        $html = get_web_page($youtubeUrl);
        preg_match('/captionTracks\\\":(.*?),\\\"audioTracks/', $html, $matches);

        if (!isset($matches[1])) {
            $this->error('Not found URL Youtube');
            $this->updateCaption([]);
            die;
        }
        $this->info('YoutubeID: ' . $youtube->youtube_id);
        $this->info('Accent: ' . $this->accent);
        $url = $this->getYoutubeBaseUrl($matches[1]);
        $this->info('URL: ' . $url);
        $this->warn(date('d/m/Y H:i:s') . ': ' . $youtube->id);
        $xml = get_web_page($url);
        if (empty($xml)) {
            $this->updateCaption([]);
        }
        $dom = new \SimpleXMLElement($xml);
        $caption = [];
        foreach ($dom[0]->text as $item) {
            $row = [];
            foreach ($item->attributes() as $key => $vl) {
                $row[$key] = $vl->__toString();
            }
            $row['text'] = trim($item->__toString());
            $caption[] = $row;
        }
        $this->updateCaption($caption);
    }

    private function refreshCommand($seconds = 1)
    {
        $this->info(PHP_EOL);
        sleep($seconds);
        $this->call('y:c');
    }

    public function updateCaption($caption)
    {
        if (empty($caption)) {
            $this->error('Error');
        }
        $this->youtubeRepository->update([
            'subtitle' => empty($caption) ? '1' : json_encode($caption)
        ], $this->yid);
        $this->refreshCommand();
    }

    private function getYoutubeBaseUrl($str, $lang = 'en')
    {

        $str = str_replace('\"', '"', $str);
        $str = str_replace('\\\'', '\'', $str);
        $str = str_replace('\\\"', '\\"', $str);
        
        $json = json_decode($str, true);
        $urls = [];
        foreach ($json as $item) {
            if (isset($item['kind'])) {
                continue;
            }
            if (!in_array($item['languageCode'], ['en', 'en-GB'])) {
                continue;
            }
            $baseUrl = $item['baseUrl'];
            $baseUrl = str_replace('\u0026', '&', $baseUrl);

            $urls[$item['languageCode']] = $baseUrl;
        }
        if (empty($urls)) {
            $this->updateCaption([]);
        }
        
        if ($this->accent == 'uk' && isset($urls['en-GB'])) {
            return $urls['en-GB'];
        } else {
            unset($urls['en-GB']);
        }

        $collect = collect($urls);
        return $collect->first();
    }
}



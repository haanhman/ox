<?php

namespace App\Console\Commands;

use App\Criteria\FirstRecordCriteria;
use App\Repositories\WordRepository;
use Illuminate\Console\Command;

class YoutubeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:youtube';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'get video UK - US';


    private $work;

    /**
     * @var WordRepository
     */
    private $wordRepository;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(WordRepository $wordRepository)
    {
        parent::__construct();
        $this->wordRepository = $wordRepository;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->wordRepository->pushCriteria(app(FirstRecordCriteria::class));
        $this->word = $this->wordRepository->findWhere(['crawler_video_done' => false])->first();
        dd($this->word);
        if (empty($this->word)) {
            $this->log('End word');
            return;
        }
        $video['uk'] = $this->fetchData('uk');
        $video['us'] = $this->fetchData('us');
        $this->updateSuccess($video);
        $this->call('fetch:youtube');
    }

    private function updateSuccess($video)
    {
        $this->wordRepository->update([
            'video_data' => json_encode($video),
            'crawler_video_done' => true
        ], $this->word->id);
        $this->log('get video data for word [' . $this->word->name . '] success');
    }


    private function fetchData($country = 'uk')
    {
        $url = 'https://youglish.com/search/' . urlencode($this->word->alias ? $this->word->alias : $this->word->name) . '/' . $country;
        $this->info('Fetch: ' . $url);
        $html = get_web_page($url);
        if (empty($html)) {
            return;
        }

        $dom = str_get_html($html);
        $scripts = $dom->find('script');
        $jsText = '';
        foreach ($scripts as $script) {
            if (strpos($script->outertext(), 'buildSharedVideo') !== false) {
                $jsText = $script->innertext();
            }
        }
        return $this->filterData($jsText);
    }

    private function filterData($jsText)
    {
        preg_match_all('/\'{.*}\'/s', $jsText, $matches);
        $json = '';
        if (isset($matches[0][0])) {
            $json = substr($matches[0][0], 1, strlen($matches[0][0]));
            $json = substr($json, 0, strlen($json) - 1);
        }

        if (empty($json)) {
            $this->noData();
            return;
        }

        $json = str_replace('\"', '"', $json);
        $json = str_replace('\\\'', '\'', $json);
        $json = str_replace('\\\"', '\\"', $json);

        $youtubeData = json_decode($json, true);
        if ($youtubeData['total'] <= 0) {
            $this->noData();
            return;
        }
        return $youtubeData;
    }

    private function noData()
    {
        $this->log('No data');
    }

    private function log($message)
    {
        $this->info($message);
        app('log')->debug($message);
    }
}

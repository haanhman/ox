<?php

namespace App\Console\Commands;

use App\Repositories\WordRepository;
use Illuminate\Console\Command;

class FetchWordHtml extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:word-html';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crawler word html';

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

    private $word;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->word = $this->wordRepository->findWhere(['crawler_done' => false])->first();
        if (empty($this->word)) {
            $this->log('End word');
            return;
        }
        $url = $this->word->url;
        $html = get_web_page($url);
        if (empty($html)) {
            return;
        }

        $dom = str_get_html($html);
        $entryContent = $dom->find('#entryContent', 0);
        if(empty($entryContent)) {
            $this->updateSuccess('');
        } else {
            $this->updateSuccess($entryContent->outertext());
        }

//        sleep(1);
        $this->call('fetch:word-html');
    }

    private function updateSuccess($html)
    {
        $this->wordRepository->update([
            'html' => $html,
            'crawler_done' => true
        ], $this->word->id);
        $this->log('get HTML for word [' . $this->word->name . '] success');
    }

    private function log($message)
    {
        $this->info($message);
        app('log')->debug($message);
    }
}

<?php

namespace App\Console\Commands;

use App\Repositories\WordRepository;
use Illuminate\Console\Command;

class WorkDetechCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'word:detech';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $this->word = $this->wordRepository->findWhere(['is_ok' => false])->first();
        if (empty($this->word)) {
            $this->log('End word');
            return;
        }
        $url = $this->word->url;
        $html = get_web_page($url);
        if (empty($html)) {
            return;
        }

        $data = $this->detechData($html);
        $this->updateSuccess($data);
        $this->call('word:detech');
    }

    private function updateSuccess($data)
    {
        $this->wordRepository->update([
            'is_ok' => true,
            'word_type' => $data['type'],
            'audio' => json_encode($data['audio']),
            'content' => json_encode($data['use'])
        ], $this->word->id);
        $this->log('boc tach HTML cho tu [' . $this->word->name . '] xong');
    }

    private function log($message)
    {
        $this->info($message);
        app('log')->debug($message);
    }

    public function detechData($html)
    {
        $dom = str_get_html($html);
        $type = $dom->find('.pos', 0);
        $data = [
            'type' => trim_space($type->text()),
            'audio' => $this->getAudio($dom),
            'use' => $this->getContent($dom)
        ];
        return $data;
    }

    private function getAudio($dom)
    {
        $audio = ['uk' => [], 'us' => []];
        $listAudio = $dom->find('.pron-g');
        if (count($listAudio) <= 0) {
            return $audio;
        }
        foreach ($listAudio as $item) {
            $uk = false;
            $sound = $item->find('.sound', 0);
            if (strpos($sound->class, 'pron-uk') !== false) {
                $uk = true;
            }
            $attr = 'data-src-mp3';
            $mp3 = trim($sound->$attr);

            $prefix = trim_space($item->find('.prefix', 0)->text());
            $phonic = $item->find('.phon', 0);
            if ($phonic->find('.bre', 0)) {
                $phonic->find('.bre', 0)->innertext = '';
            }
            if ($phonic->find('.name', 0)) {
                $phonic->find('.name', 0)->innertext = '';
            }
            $phonic->find('.separator', 0)->innertext = '';
            $phonic->find('.separator', 1)->innertext = '';
            $phonic->find('.wrap', 0)->innertext = '';
            $phonic->find('.wrap', 1)->innertext = '';
            $phonic = trim_space($phonic->text());
            $audio[$uk ? 'uk' : 'us'][] = [
                'prefix' => $prefix,
                'phonic' => $phonic,
                'mp3' => $mp3
            ];
        }
        return $audio;
    }

    private function getContent($dom)
    {
        $uses = [];
        $liUse = $dom->find('.sn-g');
        if (count($liUse) <= 0) {
            return $uses;
        }
        foreach ($liUse as $li) {
            $eg = [];
            $x = $li->find('.x');
            if (count($x) > 0) {
                foreach ($x as $e) {
                    $eg[] = trim_space($e->text());
                }
            }
            if($li->find('.def', 0)) {
                $uses[] = [
                    'desc' => trim_space($li->find('.def', 0)->text()),
                    'eg' => $eg
                ];
            }
        }
        return $uses;
    }
}

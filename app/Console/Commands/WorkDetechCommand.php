<?php

namespace App\Console\Commands;

use App\Criteria\FirstRecordCriteria;
use App\Repositories\WordRepository;
use Illuminate\Console\Command;

class WorkDetechCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'w:a';

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

    private function removeVideoError($words)
    {
        $videoError = array(
            '-2aN-UpDoNQ',
            '-cNJ97GaqLE',
            '0jRwqPsvVfE',
            '1jT_SeQ8eso',
            '1rqQpyukUDM',
            '1rSrILaL9PY',
            '2ctu8NYJLkk',
            '2oFFWtHOaaU',
            '2VVW33El-Ew',
            '3cZMEXyOAUs',
            '3FF-_b3lKhQ',
            '3Q3NblRa2X8',
            '45fFgJipjOo',
            '47bsVtJN_aE',
            '5yBU1ELFXfk',
            '74tzFCtmfbc',
            '7TCnjTX78L8',
            '873QIUSzNPM',
            '8Y4DddMXsHk',
            '93nnlpirqT0',
            '9lFPMZrYLHQ',
            'AEYQvKduCNY',
            'AF21bMBAOGI',
            'aWMCv4iOIHg',
            'cKxQMyBl22o',
            'CQBSoFxF0SA',
            'd5Dwb0h8wHg',
            'D9u3yvYx9sA',
            'dy02o7MW3VA',
            'etQJ1FUbOxk',
            'fBN5nZc65YU',
            'FJjW8bFBZQE',
            'gE4ef0yQZRU',
            'GWGG9YJXsy8',
            'haqi4xvjvKo',
            'I_kFyh3pPRE',
            'i1LL6AoFnlY',
            'IHY4pwx0-Wc',
            'IkZ1eko1JUQ',
            'iZ8so-ld-l0',
            'j1S-Q2q0Cac',
            'jHA4xN1dEkM',
            'jPGKTeAfGdE',
            'JsKjSqTxDXA',
            'KhgFXRhHP_Q',
            'kOxCtZSi_Jw',
            'krBuOL0IKFQ',
            'KyAw8aKnSxM',
            'lDBEcxL6v4A',
            'LlEhlw_d5N8',
            'lmtDZpv_09g',
            'm9nLB18X4Bc',
            'mB98vBTwVO0',
            'mT0RNrTDHkI',
            'mucyTT8EK3Y',
            'ndMmHIXTQgo',
            'nE5RGSWONZ4',
            'nmGaq9DsUfo',
            'NSWctkkwa4Y',
            'nt2OlMAJj6o',
            'NYdhk7wtz3o',
            'onfMaKyXeEQ',
            'oYQIqNX4kvc',
            'P-GtAWVzc40',
            'P5PBDe0CSxs',
            'PNAikyqTErc',
            'povSSX2r4Xc',
            'pwuuAPsE9UQ',
            'pxYqvczO6DI',
            'Qf9ORDmvBxo',
            'R-bypPCIE9g',
            'RFDhWK6RSNc',
            'rSgPZTREKQA',
            't69L1kSkMrw',
            't7thpDI75IA',
            'TZNi5uWJwXY',
            'U6S9c4UFyGk',
            'UpBycmR3_lQ',
            'V9RrWvo9ucw',
            'VLZ3gJWwvcI',
            'vrHPzuU8sIU',
            'vsbm1CbM03I',
            'WHm3xI943aA',
            'X1qD-ySmtiI',
            'xImqF2-oaf8',
            'xrUC2QKMYeg',
            'YhCWJQR7AMA',
            'yrA3-NcwsbY',
            'zOEdUV8TuCA',
            'ZQOHPBxqDcw',
            'zR6qKBC_j58',
            'Zx1qHGDTauA'
        );
        foreach ($words as $item) {
            $json = array();
            $data = json_decode($item->video_data, true);
            if(empty($data)) {
                continue;
            }
            foreach ($data as $accent => $videoData) {
                $results = collect($videoData['results']);
                foreach ($results as $v) {
                    if(in_array($v['vid'], $videoError)) {
                        continue;
                    }
                    $json[$accent][] = [
                        'start' => number_format($v['start'],2, '.', ''),
                        'vid' => $v['vid'],
                        'text' => $v['display'],
                    ];
                }
            }
            $this->wordRepository->update([
                'is_ok' => true,
                'video_data' => json_encode($json),
            ], $item->id);
        }
    }


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->wordRepository->pushCriteria(app(FirstRecordCriteria::class));
        $words = $this->wordRepository->get();

        if ($words->isEmpty()) {
            $this->log('End word');
            return;
        }

        $this->removeVideoError($words);
        $this->info('Xong');
        return;

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
            if ($li->find('.def', 0)) {
                $uses[] = [
                    'desc' => trim_space($li->find('.def', 0)->text()),
                    'eg' => $eg
                ];
            }
        }
        return $uses;
    }
}

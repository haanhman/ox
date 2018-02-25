<?php

namespace App\Console\Commands;

use App\Criteria\FirstRecordCriteria;
use App\Entities\Youtube;
use App\Repositories\YoutubeRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Criteria\RequestCriteria;

class SubtitleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'y:subtitle';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch subtitle from youtube';

    private $sub = [];
    private $youtubeRepository;
    private $start = -1;
    private $startTime = 0;
    private $yid = 0;
    private $info = null;


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
        $this->startTime = time();
        $this->youtubeRepository->pushCriteria(app(FirstRecordCriteria::class));
        $youtube = $this->youtubeRepository->first();
        if (empty($youtube)) {
            $this->log('End data');
            $this->comment('End data');
            die;
        }
        $this->yid = $youtube->id;
        $this->error('ID: ' . $this->yid);
        $this->log('ID: ' . $this->yid);
        $querys = [
            'op' => 1,
            'vid' => $youtube->youtube_id,
            'q' => 'a',
            'accent' => $youtube->accent == 'us' ? 1 : 2
        ];
        $src = 'https://youglish.com/fetchcap.jsp?' . http_build_query($querys);
//        $this->info('URL: ' . $src);
        $info = get_web_page($src);
        $this->info = $info = json_decode($info, true);
        $msg = 'S: ' . $info['start'] . ' -- E: ' . $info['end'];
        $this->warn($msg);
        $this->log($msg);


        $this->getSubTitle($info['start'], $info, $youtube);

        while (true) {
            $collection = collect($this->sub);
            $last = $collection->last();
            $start = $last['end'];
            if ($start >= $info['end']) {
                $this->comment('Start: ' . $start . ' -- End: ' . $info['end']);
                $this->log('Start: ' . $start . ' -- End: ' . $info['end']);
                break;
            }
            if (!$this->getSubTitle($start, $info, $youtube, false, $last)) {
                break;
            }
        }

        $this->updateSubtitle($youtube->id);
    }


    private function updateSubtitle($id)
    {
        $this->info(date('d/m/Y H:i:s') . ' Update subtitle: ' . $id);
        $this->log(date('d/m/Y H:i:s') . ' Update subtitle: ' . $id);
        $this->youtubeRepository->pushCriteria(app(RequestCriteria::class));

        $collect = collect($this->sub);
        $this->youtubeRepository->update([
            'subtitle' => json_encode($this->sub),
            's1' => $this->info['start'],
            'e1' => $this->info['end'],
            's2' => $collect->first()['start'],
            'e2' => $collect->last()['end'],
        ], $id);

        if (empty($this->sub)) {
            $this->error('bi BAN roi nhe');
            $this->log('bi BAN roi nhe');
            die;
        }
        $this->refreshCommand();
    }

    private function refreshCommand($seconds = 1)
    {
        $this->log(' --------------------------- OK OK ----------------------------' . PHP_EOL);
        $this->info('Total time: ' . (time() - $this->startTime) . ' s');
        $this->log('Total time: ' . (time() - $this->startTime) . ' s');
        die;
        $this->sub = [];
        $this->start = -1;
        $this->startTime = 0;
        $this->yid = 0;
        $this->info = null;
        sleep($seconds);
        $this->call('y:subtitle');
    }

    private function getSubTitle($start, $info, $youtube, $skip = false, $last = null)
    {
        if ($start == $this->start && !$skip) {
            $this->sub = [1];
            $this->updateSubtitle($this->yid);
            $this->error('Hong roi: ' . $this->start);
            $this->error('chay lai sau 3 giay');
            $this->refreshCommand(3);
            die;
        }
        $this->start = $start;
        $this->comment(date('d/m/Y H:i:s') . ' Start: ' . $this->start . ' of ---- ' . $info['end']);
        $this->log(date('d/m/Y H:i:s') . ' Start: ' . $this->start . ' of ---- ' . $info['end']);
        $querys = [
            'op' => 0,
            'q' => 'a',
            'hm' => 10,
            'from' => $this->start,
            'fid' => $info['fid'],
            'lid' => $info['lid'],
            'vid' => $info['id'],
            'id' => $youtube->cid,
            'calltime' => time(),
            'accent' => $youtube->accent == 'us' ? 1 : 2
        ];

        if ($last != null) {
            $querys = [
                'op' => 0,
                'q' => 'a',
                'hm' => 10,
                'cid' => $last['cid'],
                'id' => $last['id'],
                'fid' => $info['fid'],
                'lid' => $info['lid'],
                'vid' => $info['id'],
                'calltime' => time(),
                'accent' => $youtube->accent == 'us' ? 1 : 2
            ];
        }

        $src = 'https://youglish.com/fetchcap.jsp?' . http_build_query($querys);
//        $this->info('URL: ' . $src);
        $sub = get_web_page($src);
        $subtitle = json_decode($sub, true);
        if (!empty($subtitle['results'])) {
            foreach ($subtitle['results'] as $sub) {
                $this->sub[$sub['start']] = $sub;
            }
            return true;
        }
        return false;
    }

    private function log($msg) {
        //app('log')->debug($msg);
    }
}

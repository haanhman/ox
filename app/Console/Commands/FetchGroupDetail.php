<?php

namespace App\Console\Commands;

use App\Repositories\GroupRepository;
use App\Repositories\WordRepository;
use Illuminate\Console\Command;

class FetchGroupDetail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:group-detail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch word list of group';

    /**
     * @var GroupRepository
     */
    private $groupRepository;

    /**
     * @var WordRepository
     */
    private $wordRepository;

    private $group;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(GroupRepository $groupRepository, WordRepository $wordRepository)
    {
        parent::__construct();
        $this->groupRepository = $groupRepository;
        $this->wordRepository = $wordRepository;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->group = $this->groupRepository->findWhere(['crawler_done' => false])->first();
        if (empty($this->group)) {
            $this->log('End group');
            return;
        }
        $url = $this->group->url . '?page=' . $this->group->current_page;

        $html = get_web_page($url);
        if (empty($html)) {
            return;
        }

        $dom = str_get_html($html);
        $words = $dom->find('#entrylist1 li a');
        if (empty($words)) {
            $this->updateSuccess($this->group->id);
            return;
        }

        foreach ($words as $item) {
            $this->wordRepository->create([
                'group_id' => $this->group->id,
                'name' => trim($item->text()),
                'url' => trim($item->href)
            ]);
        }

        $this->increasePage($this->group->id, $this->group->current_page + 1);

        $paginate = $dom->find('.paging_links li a');

        $nextPage = false;
        foreach ($paginate as $p) {
            if ($p->text() == '&gt;') {
                $nextPage = true;
                break;
            }
        }
        if (!$nextPage) {
            $this->updateSuccess($this->group->id);
        }

        $this->call('fetch:group-detail');
    }

    private function increasePage($id, $page)
    {
        $this->groupRepository->update(['current_page' => $page], $id);
        $this->log('Crawler [' . $this->group->name . '] page ' . $this->group->current_page . ' DONE');
    }

    private function updateSuccess($id)
    {
        $this->groupRepository->update(['crawler_done' => true], $id);
        $this->log($this->group->name . ' crawler done');
    }

    private function log($message)
    {
        $this->info($message);
        app('log')->debug($message);
    }
}

<?php

namespace App\Console\Commands;

use App\Repositories\GroupRepository;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class FetchGroup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:group';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crawler group';

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
    public function handle(GroupRepository $groupRepository)
    {
        $url = 'https://www.oxfordlearnersdictionaries.com/wordlist/english/oxford3000/Oxford3000_A-B/';
        $html = get_web_page($url);
        if (empty($html)) {
            return;
        }

        $groupHref = [$url];
        $dom = str_get_html($html);
        $lis = $dom->find('.hide_phone li a');
        foreach ($lis as $item) {
            $groupHref[] = trim($item->href);
        }

        foreach ($groupHref as $url) {
            $arr = explode('_', $url);
            $attr = [
                'name' => $arr[1],
                'url' => $url,
                'crawler_done' => false
            ];
            $groupRepository->create($attr);
        }
        $this->info('Crawler group success');
        $this->call('fetch:group-detail');
    }
}

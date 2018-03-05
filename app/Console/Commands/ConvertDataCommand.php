<?php

namespace App\Console\Commands;

use App\Repositories\GroupRepository;
use App\Repositories\WordRepository;
use App\Repositories\YoutubeRepository;
use App\SQLite\GroupSQLite;
use App\SQLite\WordSQLite;
use App\SQLite\YoutubeSQLite;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ConvertDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:convert';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Insert data from MySQL to SQLite';

    /**
     * @var GroupRepository
     */
    private $groupRepository;

    /**
     * @var WordRepository
     */
    private $wordRepository;

    /**
     * @var YoutubeRepository
     */
    private $youtubeRepository;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        GroupRepository $groupRepository,
        WordRepository $wordRepository,
        YoutubeRepository $youtubeRepository
    ) {
        parent::__construct();
        $this->groupRepository = $groupRepository;
        $this->wordRepository = $wordRepository;
        $this->youtubeRepository = $youtubeRepository;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->truncateTable();
        $this->processGroupsTable();
        $this->processWordsTable();
        $this->processYoutubeTable();

    }

    private function processYoutubeTable()
    {
        $youtubes = $this->youtubeRepository->all();
        if ($youtubes->isEmpty()) {
            $this->log('Youtube not found');
            return;
        }

        foreach ($youtubes as $item) {
            $record = new YoutubeSQLite();
            $record->youtube_id = $item->youtube_id;
            $record->subtitle = $item->subtitle;
            $record->save();
        }
        $this->log('Insert youtube data success');
    }

    private function processWordsTable()
    {
        $words = $this->wordRepository->all();
        if ($words->isEmpty()) {
            $this->log('Word not found');
            return;
        }

        foreach ($words as $item) {
            $record = new WordSQLite();
            $record->group_id = $item->group_id;
            $record->name = $item->name;
            $record->word_type = $item->word_type;
            $record->use = $item->content;
            $record->video_data = $item->video_data;
            $record->audio_data = $item->audio;
            $record->save();
        }
        $this->log('Insert word data success');
    }

    private function processGroupsTable()
    {
        $groups = $this->groupRepository->all();
        if ($groups->isEmpty()) {
            $this->log('Group not found');
            return;
        }

        foreach ($groups as $item) {
            $record = new GroupSQLite();
            $record->id = $item->id;
            $record->name = $item->name;
            $record->save();
        }
        $this->log('Insert group data success');
    }

    private function truncateTable()
    {
        GroupSQLite::truncate();
        $this->warn('Truncate table: groups');
        WordSQLite::truncate();
        $this->warn('Truncate table: words');
        YoutubeSQLite::truncate();
        $this->warn('Truncate table: youtube');
    }

    private function log($message)
    {
        $this->info($message);
    }
}

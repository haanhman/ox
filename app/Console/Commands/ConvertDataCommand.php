<?php

namespace App\Console\Commands;

use App\Repositories\GroupRepository;
use App\Repositories\WordRepository;
use App\Repositories\YoutubeRepository;
use App\Repositories\WordTypeRepository;
use App\SQLite\GroupSQLite;
use App\SQLite\WordSQLite;
use App\SQLite\YoutubeSQLite;
use App\SQLite\WordTypeSQLite;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Criteria\FirstRecordCriteria;

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

    private $wordTypeRepository;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        GroupRepository $groupRepository,
        WordRepository $wordRepository,
        YoutubeRepository $youtubeRepository,
        WordTypeRepository $wordTypeRepository
    ) {
        parent::__construct();
        $this->groupRepository = $groupRepository;
        $this->wordRepository = $wordRepository;
        $this->youtubeRepository = $youtubeRepository;
        $this->wordTypeRepository = $wordTypeRepository;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // $this->truncateTable();
        // $this->processWordTypeTable();
        // $this->processGroupsTable();
        // $this->processWordsTable();        
        $this->processYoutubeTable();
    }

    private function processWordTypeTable()
    {
        $results = $this->wordTypeRepository->all();
        if ($results->isEmpty()) {
            $this->log('Word type not found');
            return;
        }

        foreach ($results as $item) {
            $record = new WordTypeSQLite();
            $record->weight = $item->weight;
            $record->name = $item->name;
            $record->save();
        }
        $this->log('Insert word_type data success');
    }

    private function processYoutubeTable()
    {
        $this->youtubeRepository->pushCriteria(app(FirstRecordCriteria::class));
        $youtubes = $this->youtubeRepository->all(['youtube_id', 'subtitle']);        
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
        $words = $this->wordRepository->all(['group_id', 'name', 'wt', 'content', 'video_data', 'audio']);
        if ($words->isEmpty()) {
            $this->log('Word not found');
            return;
        }

        foreach ($words as $item) {
            $record = new WordSQLite();
            $record->group_id = $item->group_id;
            $record->name = $item->name;
            $record->word_type = $item->wt;
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
        // WordTypeSQLite::truncate();
        // $this->warn('Truncate table: word_type');
        // GroupSQLite::truncate();
        // $this->warn('Truncate table: groups');
        // WordSQLite::truncate();
        // $this->warn('Truncate table: words');
        YoutubeSQLite::truncate();
        $this->warn('Truncate table: youtube');
    }

    private function log($message)
    {
        $this->info($message);
    }
}

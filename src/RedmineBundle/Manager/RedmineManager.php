<?php

namespace RedmineBundle\Manager;

use Redmine\Client;

class RedmineManager
{
    /**
     * @var Client
     */
    protected $redmineCleint;

    /**
     * RedmineManager constructor.
     * @param Client $redmineClient
     */
    public function __construct(Client $redmineClient)
    {
        $this->redmineCleint = $redmineClient;
    }

    /**
     * Get all time entries by project and return sum of time
     *
     * @param $projectId project ID
     * @return int project time
     */
    public function getAllProjectTime($projectId)
    {
        $timeEntries = [];
        $projectTimeEntries = ['total_count' => 1];

        while (count($timeEntries) < $projectTimeEntries['total_count']) {
            $projectTimeEntries = $this->redmineCleint->time_entry->all([
                'project_id' => $projectId,
                'limit' => 100,
                'offset' => count($timeEntries),
            ]);

            $timeEntries = array_merge($timeEntries, $projectTimeEntries['time_entries']);
        }

        $allTime = 0;

        foreach ($timeEntries as $entry) {
            $allTime = $allTime + $entry['hours'];
        }

        return $allTime;
    }

}
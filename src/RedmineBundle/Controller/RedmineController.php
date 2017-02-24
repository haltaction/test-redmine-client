<?php

namespace RedmineBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class RedmineController extends Controller
{
    /**
     * Show list of projects
     *
     * @return Response
     */
    public function listProjectAction()
    {
        $projects = $this->get('redmine.client')->project->all();

        if (!$projects) {
            return new Response("Api connect error", 500);
        }

        return $this->render('RedmineBundle:Redmine:projects_list.html.twig', [
            'projects' => $projects,
        ]);
    }

    /**
     * Show project and track time by project
     *
     * @param $projectId
     *
     * @return Response
     */
    public function showProjectAction($projectId)
    {
        $project = $this->get('redmine.client')->project->show($projectId);
        if (!$project) {
            $this->createNotFoundException();
        }

        $projectTime = $this->get('redmine.manager')->getAllProjectTime($projectId);

        return $this->render('RedmineBundle:Redmine:project_show.html.twig', [
            'project' => $project['project'],
            'projectTime' => $projectTime,
        ]);
    }

    /**
     * Show issues list with pagination
     *
     * @param $projectId
     * @param $page
     *
     * @return Response
     */
    public function issuesListAction($projectId, $page)
    {
        $limitIssue = 25;
        $offset = ($page - 1) * $limitIssue;
        $projectIssues = $this->get('redmine.client')->issue->all([
            'project_id' => $projectId,
            'offset' => $offset,
            'limit' => $limitIssue,
        ]);

        return $this->render('RedmineBundle:Redmine:issues_list.html.twig', [
            'projectId' => $projectId,
            'projectIssues' => $projectIssues,
        ]);
    }

    /**
     * Show issue
     *
     * @param $projectId
     * @param $issueId
     *
     * @return Response
     */
    public function showIssueAction($projectId, $issueId)
    {
        $issue = $this->get('redmine.client')->issue->show($issueId);

        if (!$issue) {
            return new Response("Wrong issue id", 400);
        }

        return $this->render('RedmineBundle:Redmine:issue_show.html.twig', [
            'issue' => $issue,
        ]);
    }

}

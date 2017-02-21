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
        $projects = $this->container->get('redmine.client')->project->all();

        if (!$projects) {
            return new Response("Api connect error", 500);
        }

        return $this->render('RedmineBundle:Project:projects_list.html.twig', [
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
        $project = $this->container->get('redmine.client')->project->show($projectId);

        $projectTime = $this->container->get('redmine.manager')->getAllProjectTime($projectId);

        if (!$project) {
            return new Response("Wrong project id", 400);
        }

        return $this->render('RedmineBundle:Project:project_show.html.twig', [
            'projectId' => $projectId,
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
        $projectIssues = $this->container->get('redmine.client')->issue->all([
            'project_id' => $projectId,
            'offset' => $offset,
            'limit' => $limitIssue,
        ]);

        return $this->render('RedmineBundle:Project:issues_list.html.twig', [
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
        $issue = $this->container->get('redmine.client')->issue->show($issueId);

        if (!$issue) {
            return new Response("Wrong issue id", 400);
        }

        return $this->render('@Redmine/Project/issue_show.html.twig', [
            'issue' => $issue,
        ]);
    }

}

<?php

namespace RedmineBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ProjectController extends Controller
{
    /**
     * @return Response
     */
    public function listAction()
    {
        $projects = $this->container->get('redmine.client')->project->all();

        if (!$projects) {
            return new Response("Api connect error", 500);
        }

        return $this->render('RedmineBundle:Project:list.html.twig', [
            'projects' => $projects,
        ]);
    }

    /**
     * @param $projectId
     *
     * @return Response
     */
    public function showProjectAction($projectId, $page)
    {
        $project = $this->container->get('redmine.client')->project->show($projectId);

        $projectTime = $this->container->get('redmine.manager')->getAllProjectTime($projectId);

        $limitIssue = 25;
        $offset = ($page - 1) * $limitIssue;
        $projectIssues = $this->container->get('redmine.client')->issue->all([
            'project_id' => $projectId,
            'offset' => $offset,
            'limit' => $limitIssue,
        ]);

        if (!$project) {
            return new Response("Wrong project id", 400);
        }

        return $this->render('RedmineBundle:Project:show.html.twig', [
            'project' => $project['project'],
            'projectTime' => $projectTime,
            'projectIssues' => $projectIssues,
        ]);
    }

}

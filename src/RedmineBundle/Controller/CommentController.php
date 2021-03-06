<?php

namespace RedmineBundle\Controller;

use RedmineBundle\Entity\Comment;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;

class CommentController extends Controller
{
    /**
     * Create new comment.
     *
     * @param $projectId
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createCommentAction($projectId, Request $request)
    {
        $user = $this->getUser();
        $comment = new Comment();
        $comment->setProjectId($projectId);
        $comment->setUsername($user->getUsername());
        $comment->setCreatedAt(new \DateTime('now'));

        $form = $this->createFormBuilder($comment)
            ->setMethod('POST')
            ->setAction($this->generateUrl('project_show', [
                'projectId' => $projectId,
            ]))
            ->add('text', TextareaType::class)
            ->add('create', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('project_show', [
                'projectId' => $projectId,
            ]);
        // if form not valid, render project view with errors
        } elseif ($form->isSubmitted()) {
            $project = $this->get('redmine.client')->project->show($projectId);
            $projectTime = $this->get('redmine.manager')->getAllProjectTime($projectId);

            return $this->render('RedmineBundle:Comment:comment_form_error.html.twig', [
                'commentForm' => $form->createView(),
                'project' => $project['project'],
                'projectTime' => $projectTime,
            ]);
        }

        return $this->render('RedmineBundle:Comment:comment_form.html.twig', [
            'commentForm' => $form->createView(),
        ]);
    }

    /**
     * Show comments list.
     *
     * @param $projectId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listCommentAction($projectId)
    {
        $comments = $this->get('redmine.comment.repository')
            ->getAllByProject($projectId)
            ->execute();

        return $this->render('RedmineBundle:Comment:comment_list.html.twig', [
            'comments' => $comments,
        ]);
    }
}

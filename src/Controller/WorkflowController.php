<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\BeerGlass;
use App\Repository\BeerGlassRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Workflow\Registry;

class WorkflowController extends AbstractController
{

    /**
     * WorkflowController constructor.
     *
     */
    public function __construct(
        private EntityManagerInterface $dm,
        private Registry $registry,
        private BeerGlassRepository $beerGlassRepository,
    )
    {
    }

    /**
     * @Route("/workflow/", name="workflow", methods={"GET"})
     */
    public function index()
    {
        $workflows = [
            'beerglass_basic' => 'clean',
            'beerglass_2' => 'clean',
            'beerglass_complex' => 'clean',
            'complex_events' => 'clean',
        ];

        return $this->render('workflow/index.html.twig', [
            'workflows' => $workflows,
        ]);
    }

    /**
     * @Route("/workflow", name="create_workflow", methods={"POST"})
     */
    public function createWorkflow(Request $request)
    {
        $workflowName = $request->request->get('workflow_name');
        $workflowStartingState = $request->request->get('state');

        $doc = new BeerGlass();
        if ($workflowStartingState) {
            $doc->setState($workflowStartingState);
        }

        $this->dm->persist($doc);
        $this->dm->flush();

        return $this->redirect($this->generateUrl('show_workflow', [
            'workflowName' => $workflowName,
            'id' => $doc->getId(),
        ]));
    }

    /**
     * @param string $workflowName
     * @param string id
     * @Route("/workflow/{workflowName}/{id}", name="show_workflow", methods={"GET"})
     */
    public function workflow(string $workflowName, string $id)
    {
        $beerGlass = $this->dm->find(BeerGlass::class, $id);
        $workflow = $this->registry->get($beerGlass, $workflowName);

        return $this->render('workflow/show.html.twig', [
            'workflowName' => $workflowName,
            'workflow' => $workflow,
            'beerGlass' => $beerGlass,
        ]);
    }

    /**
     * @Route("/workflow/transition/{id}", methods={"POST"})
     */
    public function transition(string $id, Request $request)
    {
        $beerGlass = $this->dm->find(BeerGlass::class, $id);
        $workflowName = $request->request->get('workflow_name');
        $transition = $request->request->get('transition');
        $workflow = $this->registry->get($beerGlass, $workflowName);
        $workflow->apply($beerGlass, $transition);
        $this->dm->flush();

        return $this->redirect($this->generateUrl('show_workflow',
            ['workflowName' => $workflowName, 'id' => $beerGlass->getId()]));
    }
}

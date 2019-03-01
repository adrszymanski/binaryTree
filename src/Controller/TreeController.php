<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request};

/**
 * @Route("/nodes")
 */
class TreeController extends AbstractController {
    const NODE_LIMIT = 1000;

    /**
     * @Route("", name="get_nodes", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function getNodes(Request $request) {
        $startingRow = $request->get('startingRow', 0);
        $endingRow = $request->get('endingRow', $startingRow);
        $page = $request->get('page', 1);
        $limit = self::NODE_LIMIT;
        $startingPoint = $page * $limit;

        $entityManager = $this->getDoctrine()->getManager();
        $query = $entityManager->createQuery("
            SELECT n FROM App\Entity\Node n
            WHERE n.depth >= {$startingRow} AND 
            n.depth <= {$endingRow}
        ");
        $query->setFirstResult($startingPoint);
        $query->setMaxResults($limit);

        return $this->json($query->getResult());
    }

    /**
     * @Route("/numberOfRows", name="get_rows", methods={"GET"})
     */
    public function getNumberOfRows() {
        $entityManager = $this->getDoctrine()->getManager();
        $query = $entityManager->createQuery('
            SELECT MAX(n.depth) AS lastRow 
            FROM App\Entity\Node n
        ');

        return $this->json($query->getResult());
    }

    /**
     * @Route("/numberOfNodes", name="get_number_of_nodes", methods={"GET"})
     */
    public function getNumberOfNodes(Request $request) {
        $row = $request->get('row', 0);
        $entityManager = $this->getDoctrine()->getManager();
        $query = $entityManager->createQuery("
            SELECT COUNT(n.id) AS numberOfNodes 
            FROM App\Entity\Node n
            WHERE n.depth = {$row}
        ");

        return $this->json($query->getResult());
    }
}
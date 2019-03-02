<?php

namespace App\Controller;

use App\Entity\Node;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/nodes")
 */
class TreeController extends AbstractController {
    const NODE_LIMIT = 1000;

    private $createdNodeParents = '';

    /**
     * @Route("", name="get_nodes", methods={"GET"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
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
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
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

    /**
     * @Route("/{id}", name="delete_node", methods={"DELETE"})
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Doctrine\ORM\ORMException
     */
    public function deleteNode(int $id) {
        $repository = $this->getDoctrine()
            ->getRepository(Node::class);
        $node = $repository->find($id);

        $children = $repository->findBy(['parentId' => $node->getId()]);
        if (sizeof($children)) {
            return new JsonResponse(
                'This not is not a leaf.',
                Response::HTTP_CONFLICT
            );
        }
        $entityManager = $this->getDoctrine()->getManager();
        $this->updateParentState($node, $entityManager, -1);
        $entityManager->remove($node);
        $entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route("/add", name="create_node", methods={"POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Doctrine\ORM\ORMException
     */
    public function createNode(Request $request) {
        /**
         * @var Serializer $serializer
         */
        $serializer = $this->get('serializer');

        $node = $serializer->deserialize(
            $request->getContent(),
            Node::class,
            'json'
        );

        $repository = $this->getDoctrine()
            ->getRepository(Node::class);
        $parentsChild = $repository->findBy([
            'parentId' => $node->getParentId(),
            'isLeft' => $node->getIsLeft()
        ]);
        if ($parentsChild) {
            $side = $node->getIsLeft() ? 'left' : 'right';
            return new JsonResponse(
                "This node already has {$side} child.",
                Response::HTTP_CONFLICT
            );
        } else {
            $entityManager = $this->getDoctrine()->getManager();
            $this->updateParentState($node, $entityManager, 1);
            $node->setParents($this->createdNodeParents);
            $this->createdNodeParents = '';
            $entityManager->persist($node);
            $entityManager->flush();
        }
        return $this->json($node);
    }

    /**
     * Updates recursively all parents credit states(addition or
     * subtraction based on method used)
     *
     * @param App\Entity\Node $node
     * @param EntityManager $entityManager
     * @param int $num
     * @throws \Doctrine\ORM\ORMException
     */
    private function updateParentState($node, $entityManager, $num) {
        $repository = $this->getDoctrine()
            ->getRepository(Node::class);
        $parentId = $node->getParentId();
        $parent = $repository->find($parentId);
        $this->createdNodeParents = "/{$parentId}" . $this->createdNodeParents;

        if ($node->getIsLeft()) {
            $parent->setCreditsLeft($parent->getCreditsLeft() + $num);
        } else {
            $parent->setCreditsRight($parent->getCreditsRight() + $num);
        }
        $entityManager->persist($parent);

        if ($parent->getParentId()) {
            $this->updateParentState($parent, $entityManager, $num);
        }
    }
}
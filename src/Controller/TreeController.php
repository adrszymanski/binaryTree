<?php

namespace App\Controller;

use App\Entity\Node;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/nodes")
 */
class TreeController extends AbstractController {

    private $items;

    /**
     * @Route("/", name="get_nodes", methods={"GET"})
     */
    public function getNodes() {
        $repository = $this->getDoctrine()
            ->getRepository(Node::class);
        $this->items = $repository->findAll();

        $root = array_filter($this->items, function($element) {
            return $element->getParentId() == null;
        })[0];
        $tree = [];
        $this->handleNode($root, $tree, true);
        return $this->json($tree);
    }

    /**
     * Recursive function which adds current element to its parent
     * and call it for its left and right child.
     *
     * @param $node {object}
     * @param $childrenArray {array}
     * @param $rootFill {boolean}
     */
    public function handleNode($node, &$childrenArray, $rootFill) {
        $newNode['id'] = $node->getId();
        $newNode['name'] = $node->getName();
        $newNode['creditsLeft'] = $node->getCreditsLeft();
        $newNode['creditsRight'] = $node->getCreditsRight();
        $newNode['isLeft'] = $node->getIsLeft();
        $newNode['children'] = [];
        array_push($childrenArray, $newNode);

        $index = $rootFill ? 0 : ($node->getIsLeft() ? 0 : 1);
        $this->handleChild(true, $node->getId(),
            $index, $childrenArray);
        $this->handleChild(false, $node->getId(),
            $index, $childrenArray);
    }

    /**
     * Calling handleNode for each child.
     *
     * @param $isLeft {boolean}
     * @param $parentId {int}
     * @param $index {int}
     * @param $parentsChildren {array}
     */
    public function handleChild($isLeft, $parentId, $index, &$parentsChildren) {
        $child = array_filter($this->items,
            function($element) use ($parentId, $isLeft) {
                return
                   $element->getParentId() == $parentId &&
                   $element->getIsLeft() == $isLeft
                ;
        });
        if (sizeof($child)) {
            $this->handleNode(current($child),
                $parentsChildren[$index]['children'], false);
        }
    }

    /**
     * @Route("/{id}", name="delete_node", methods={"DELETE"})
     * @param $id
     * @return JsonResponse
     */
    public function deleteNode($id) {
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
            $entityManager->persist($node);
            $this->updateParentState($node, $entityManager, 1);
            $entityManager->flush();
        }

        return $this->json($node);
    }

    /**
     * Updates recursively all parents credit states(addition or
     * subtraction based on method used)
     *
     * @param $node
     * @param $entityManager
     * @param $num
     */
    public function updateParentState($node, $entityManager, $num) {
        $repository = $this->getDoctrine()
            ->getRepository(Node::class);
        $parent = $repository->find($node->getParentId());

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
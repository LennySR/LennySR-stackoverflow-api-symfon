<?php

namespace App\Controller;

use App\Entity\Query;
use App\Entity\Question;
use App\Service\StackOverflowApiService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class StackOverflowController extends AbstractController
{
    private $apiService;

    public function __construct(StackOverflowApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * @Route("/api/questions", name="api_questions", methods={"GET"})
     */
    public function getQuestions(Request $request, EntityManagerInterface $em): JsonResponse
    {
        // Obtener y validar parámetros
        $tagged = $request->query->get('tagged');
        if (!$tagged) {
            return new JsonResponse(['error' => 'El parámetro "tagged" es obligatorio.'], 400);
        }

        $fromDate = $request->query->get('fromdate') ? strtotime($request->query->get('fromdate')) : null;
        $toDate = $request->query->get('todate') ? strtotime($request->query->get('todate')) : null;

        // Verificar si la consulta ya existe en la base de datos
        $existingQuery = $em->getRepository(Query::class)->findOneBy([
            'tagged' => $tagged,
            'fromDate' => $fromDate ? new \DateTime("@$fromDate") : null,
            'toDate' => $toDate ? new \DateTime("@$toDate") : null,
        ]);

        if ($existingQuery) {
            // Convertir preguntas a formato JSON
            $data = $existingQuery->getQuestions()->map(function (Question $question) {
                return [
                    'title' => $question->getTitle(),
                    'creation_date' => $question->getCreationDate()->format('Y-m-d H:i:s'),
                    'body' => $question->getBody(),
                    'tags' => $question->getTags()
                ];
            })->toArray();

            return new JsonResponse($data, 200);
        }

        // Si no existe, llamar a la API y guardar los resultados
        try {
            $questionsData = $this->apiService->fetchQuestions($tagged, $fromDate, $toDate);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Error al consultar la API externa.'], 500);
        }

        // Crear una nueva consulta y asociar las preguntas
        $newQuery = new Query();
        $newQuery->setTagged($tagged);
        $newQuery->setFromDate($fromDate ? new \DateTime("@$fromDate") : null);
        $newQuery->setToDate($toDate ? new \DateTime("@$toDate") : null);

        foreach ($questionsData as $questionData) {
            $question = new Question();
            $question->setTitle($questionData['title']);
            $question->setCreationDate(new \DateTime('@' . $questionData['creation_date']));
            $question->setBody($questionData['body'] ?? null);
            $question->setTags($tagged); // Almacenar los tags como están en la API
            $question->setQuery($newQuery); // Asociar con la consulta actual
            $newQuery->addQuestion($question); // Agregar la pregunta a la consulta
        }

        $em->persist($newQuery);
        $em->flush();

        // Respuesta con los datos de la API
        $data = array_map(function ($questionData) {
            return [
                'title' => $questionData['title'],
                'creation_date' => date('Y-m-d H:i:s', $questionData['creation_date']),
                'body' => $questionData['body'] ?? null,
                'tags' => $questionData['tags']
            ];
        }, $questionsData);

        return new JsonResponse($data, 200);
    }
}

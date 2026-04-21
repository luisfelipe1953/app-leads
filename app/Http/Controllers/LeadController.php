<?php

namespace App\Http\Controllers;

use App\Http\Requests\AiSummaryRequest;
use App\Http\Requests\StoreLeadRequest;
use App\Http\Requests\UpdateLeadRequest;
use App\Http\Resources\LeadResource;
use App\Services\AiSummaryService;
use App\Services\LeadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Attributes as OA;

class LeadController extends Controller
{
    public function __construct(
        private readonly LeadService $leadService,
        private readonly AiSummaryService $aiService
    ) {}

    #[OA\Get(
        path: '/api/leads',
        summary: 'Listar leads con paginación y filtros',
        security: [['apiKey' => []]],
        tags: ['Leads'],
        parameters: [
            new OA\Parameter(name: 'page', in: 'query', schema: new OA\Schema(type: 'integer', default: 1)),
            new OA\Parameter(name: 'limit', in: 'query', schema: new OA\Schema(type: 'integer', default: 15)),
            new OA\Parameter(name: 'fuente', in: 'query', schema: new OA\Schema(type: 'string', enum: ['instagram', 'facebook', 'landing_page', 'referido', 'otro'])),
            new OA\Parameter(name: 'fecha_inicio', in: 'query', schema: new OA\Schema(type: 'string', format: 'date')),
            new OA\Parameter(name: 'fecha_fin', in: 'query', schema: new OA\Schema(type: 'string', format: 'date')),
        ],
        responses: [new OA\Response(response: 200, description: 'Lista de leads paginada')]
    )]
    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = $request->only(['fuente', 'fecha_inicio', 'fecha_fin']);
        $page    = (int) $request->get('page', 1);
        $limit   = (int) $request->get('limit', 15);

        $leads = $this->leadService->list($filters, $page, $limit);

        return LeadResource::collection($leads);
    }

    #[OA\Post(
        path: '/api/leads',
        summary: 'Registrar un nuevo lead',
        security: [['apiKey' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['nombre', 'email', 'fuente'],
                properties: [
                    new OA\Property(property: 'nombre', type: 'string', example: 'Juan Pérez'),
                    new OA\Property(property: 'email', type: 'string', example: 'juan@example.com'),
                    new OA\Property(property: 'telefono', type: 'string', example: '+573001234567'),
                    new OA\Property(property: 'fuente', type: 'string', enum: ['instagram', 'facebook', 'landing_page', 'referido', 'otro']),
                    new OA\Property(property: 'producto_interes', type: 'string', example: 'Curso de Marketing'),
                    new OA\Property(property: 'presupuesto', type: 'number', example: 500),
                ]
            )
        ),
        tags: ['Leads'],
        responses: [
            new OA\Response(response: 201, description: 'Lead creado exitosamente'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    public function store(StoreLeadRequest $request): JsonResponse
    {
        $lead = $this->leadService->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Lead registrado exitosamente.',
            'data'    => new LeadResource($lead),
        ], 201);
    }

    #[OA\Get(
        path: '/api/leads/stats',
        summary: 'Estadísticas de leads',
        security: [['apiKey' => []]],
        tags: ['Leads'],
        responses: [new OA\Response(response: 200, description: 'Estadísticas generales')]
    )]
    public function stats(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => $this->leadService->stats(),
        ]);
    }

    #[OA\Get(
        path: '/api/leads/{id}',
        summary: 'Obtener un lead por ID',
        security: [['apiKey' => []]],
        tags: ['Leads'],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Lead encontrado'),
            new OA\Response(response: 404, description: 'Lead no encontrado'),
        ]
    )]
    public function show(int $id): JsonResponse
    {
        $lead = $this->leadService->find($id);

        return response()->json([
            'success' => true,
            'data'    => new LeadResource($lead),
        ]);
    }

    #[OA\Patch(
        path: '/api/leads/{id}',
        summary: 'Actualizar un lead',
        security: [['apiKey' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(properties: [
                new OA\Property(property: 'nombre', type: 'string'),
                new OA\Property(property: 'telefono', type: 'string'),
                new OA\Property(property: 'producto_interes', type: 'string'),
                new OA\Property(property: 'presupuesto', type: 'number'),
            ])
        ),
        tags: ['Leads'],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Lead actualizado'),
            new OA\Response(response: 404, description: 'Lead no encontrado'),
        ]
    )]
    public function update(UpdateLeadRequest $request, int $id): JsonResponse
    {
        $lead = $this->leadService->update($id, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Lead actualizado exitosamente.',
            'data'    => new LeadResource($lead),
        ]);
    }

    #[OA\Delete(
        path: '/api/leads/{id}',
        summary: 'Eliminar un lead (soft delete)',
        security: [['apiKey' => []]],
        tags: ['Leads'],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Lead eliminado'),
            new OA\Response(response: 404, description: 'Lead no encontrado'),
        ]
    )]
    public function destroy(int $id): JsonResponse
    {
        $this->leadService->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Lead eliminado exitosamente.',
        ]);
    }

    #[OA\Post(
        path: '/api/leads/ai/summary',
        summary: 'Generar resumen ejecutivo con IA (OpenAI)',
        security: [['apiKey' => []]],
        requestBody: new OA\RequestBody(
            required: false,
            content: new OA\JsonContent(properties: [
                new OA\Property(property: 'fuente', type: 'string', enum: ['instagram', 'facebook', 'landing_page', 'referido', 'otro']),
                new OA\Property(property: 'fecha_inicio', type: 'string', format: 'date', example: '2024-01-01'),
                new OA\Property(property: 'fecha_fin', type: 'string', format: 'date', example: '2024-12-31'),
            ])
        ),
        tags: ['IA'],
        responses: [new OA\Response(response: 200, description: 'Resumen generado por IA')]
    )]
    public function aiSummary(AiSummaryRequest $request): JsonResponse
    {
        $filters = $request->validated();
        $leads   = $this->leadService->getFiltered($filters);
        $summary = $this->aiService->generateSummary($leads, $filters);

        return response()->json([
            'success'     => true,
            'total_leads' => $leads->count(),
            'filtros'     => $filters,
            'resumen'     => $summary,
        ]);
    }

    #[OA\Post(
        path: '/api/leads/webhook',
        summary: 'Webhook simulando integración Typeform',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(properties: [
                new OA\Property(
                    property: 'form_response',
                    type: 'object',
                    properties: [
                        new OA\Property(
                            property: 'answers',
                            type: 'array',
                            items: new OA\Items(
                                type: 'object',
                                properties: [
                                    new OA\Property(property: 'field', type: 'object', properties: [new OA\Property(property: 'ref', type: 'string')]),
                                    new OA\Property(property: 'text', type: 'string'),
                                    new OA\Property(property: 'email', type: 'string'),
                                    new OA\Property(property: 'number', type: 'number'),
                                ]
                            )
                        ),
                    ]
                ),
            ])
        ),
        tags: ['Webhook'],
        responses: [
            new OA\Response(response: 201, description: 'Lead creado desde webhook'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    public function webhook(Request $request): JsonResponse
    {
        $answers = $request->input('form_response.answers', []);

        $data = [];
        foreach ($answers as $answer) {
            $ref = $answer['field']['ref'] ?? null;
            $data[$ref] = $answer['text'] ?? $answer['email'] ?? $answer['number'] ?? null;
        }

        $payload = [
            'nombre'           => $data['nombre'] ?? null,
            'email'            => $data['email'] ?? null,
            'telefono'         => $data['telefono'] ?? null,
            'fuente'           => $data['fuente'] ?? 'otro',
            'producto_interes' => $data['producto_interes'] ?? null,
            'presupuesto'      => isset($data['presupuesto']) ? (float) $data['presupuesto'] : null,
        ];

        $validator = validator($payload, [
            'nombre' => ['required', 'string', 'min:2'],
            'email'  => ['required', 'email'],
            'fuente' => ['required', 'in:instagram,facebook,landing_page,referido,otro'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos del webhook inválidos.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $lead = $this->leadService->create($payload);

        return response()->json([
            'success' => true,
            'message' => 'Lead creado desde webhook.',
            'data'    => new LeadResource($lead),
        ], 201);
    }
}

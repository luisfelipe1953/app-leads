<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Http;

class AiSummaryService
{
    public function generateSummary(Collection $leads, array $filters): string
    {
        if ($leads->isEmpty()) {
            return 'No se encontraron leads con los filtros proporcionados para generar un resumen.';
        }

        $prompt = $this->buildPrompt($leads, $filters);

        $apiKey = config('services.openai.key');

        if (empty($apiKey) || $apiKey === 'your_openai_api_key_here') {
            return $this->mockSummary($leads);
        }

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$apiKey}",
            'Content-Type'  => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    'role'    => 'system',
                    'content' => 'Eres un analista de marketing especializado en embudos de ventas digitales. Responde en español, de forma concisa y ejecutiva.',
                ],
                [
                    'role'    => 'user',
                    'content' => $prompt,
                ],
            ],
            'max_tokens'  => 600,
            'temperature' => 0.7,
        ]);

        if ($response->failed()) {
            return $this->mockSummary($leads);
        }

        return $response->json('choices.0.message.content', $this->mockSummary($leads));
    }

    private function buildPrompt(Collection $leads, array $filters): string
    {
        $total = $leads->count();
        $fuenteConteo = $leads->groupBy('fuente')->map->count()->toArray();
        $fuentePrincipal = array_key_first(arsort($fuenteConteo) ? $fuenteConteo : $fuenteConteo);
        $promedio = $leads->whereNotNull('presupuesto')->avg('presupuesto');

        $resumenLeads = $leads->map(fn($l) => [
            'nombre'           => $l->nombre,
            'fuente'           => $l->fuente,
            'producto_interes' => $l->producto_interes,
            'presupuesto'      => $l->presupuesto,
        ])->toJson(JSON_UNESCAPED_UNICODE);

        $filtrosTexto = !empty($filters)
            ? 'Filtros aplicados: ' . json_encode($filters, JSON_UNESCAPED_UNICODE)
            : 'Sin filtros (todos los leads)';

        return <<<PROMPT
        Analiza los siguientes {$total} leads de marketing digital y genera un resumen ejecutivo.

        {$filtrosTexto}
        Fuente principal: {$fuentePrincipal}
        Promedio de presupuesto: \${$promedio} USD

        Datos de leads:
        {$resumenLeads}

        Incluye en tu respuesta:
        1. Análisis general de los leads
        2. Canal principal de adquisición y su rendimiento
        3. Segmento de presupuesto predominante
        4. Al menos 3 recomendaciones accionables para el equipo de ventas
        PROMPT;
    }

    private function mockSummary(Collection $leads): string
    {
        $total = $leads->count();
        $fuenteConteo = $leads->groupBy('fuente')->map->count();
        $fuentePrincipal = $fuenteConteo->sortDesc()->keys()->first() ?? 'N/A';
        $promedio = round($leads->whereNotNull('presupuesto')->avg('presupuesto') ?? 0, 2);

        return <<<TEXT
        [MOCK - Configurar OPENAI_API_KEY para respuesta real]

        📊 RESUMEN EJECUTIVO DE LEADS

        Se analizaron {$total} leads en total. El canal de adquisición principal es "{$fuentePrincipal}", concentrando la mayor parte del tráfico entrante. El presupuesto promedio de los leads es de \${$promedio} USD.

        🎯 Análisis general:
        Los leads muestran una distribución variada por canal. La mayoría proviene de canales digitales activos, lo que indica una estrategia de marketing multicanal en funcionamiento.

        📢 Canal principal:
        "{$fuentePrincipal}" es la fuente dominante. Se recomienda aumentar la inversión en este canal dado su volumen de leads generados.

        💡 Recomendaciones:
        1. Priorizar el seguimiento de leads con presupuesto declarado mayor a \$500 USD.
        2. Implementar un flujo de nurturing específico para leads sin presupuesto declarado.
        3. Evaluar la calidad vs cantidad de leads por fuente para optimizar el CAC.
        TEXT;
    }
}

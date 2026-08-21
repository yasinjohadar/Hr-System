<?php

namespace App\Support;

use RuntimeException;

/**
 * مُقيِّم صيغ حسابية آمن — بديل eval().
 *
 * يدعم: الأرقام، المتغيّرات بالاسم، + - * / %، الأقواس، والسالب الأحادي.
 * ولا يدعم — بقصد — أي شيء آخر: لا دوال، لا استدعاءات، لا متغيّرات PHP.
 *
 * سبب وجوده: كان PayrollController يقيّم صيغة مكوّن الراتب بـ
 * eval("return $formula;") على نصّ قادم من قاعدة البيانات. أي شخص يستطيع
 * تعديل صيغة مكوّن راتب كان يستطيع تنفيذ كود PHP على الخادم. وكان يتعطّل
 * أيضاً: المعرّف غير المعروف يرمي \Error لا \Exception، فلا يلتقطه
 * catch (\Exception) ويصبح 500.
 *
 * هنا: المعرّف غير المعروف يرمي RuntimeException يلتقطه المُنادي فيسجّله
 * ويُرجع صفراً — تدهور لطيف لا انهيار.
 */
class FormulaEvaluator
{
    /** @var array<string, float> */
    private array $variables;

    private string $expression;

    private int $position = 0;

    /**
     * @param  array<string, float|int>  $variables
     */
    public function __construct(array $variables = [])
    {
        $this->variables = array_map(static fn ($v) => (float) $v, $variables);
    }

    /**
     * @param  array<string, float|int>  $variables
     *
     * @throws RuntimeException عند صيغة غير صالحة أو متغيّر غير معروف
     */
    public static function evaluate(string $formula, array $variables = []): float
    {
        return (new self($variables))->run($formula);
    }

    /**
     * @throws RuntimeException
     */
    public function run(string $formula): float
    {
        // اقبل الصيغتين: {base_salary} و base_salary
        $this->expression = str_replace(['{', '}'], '', $formula);
        $this->position = 0;

        $value = $this->parseExpression();
        $this->skipWhitespace();

        if ($this->position < strlen($this->expression)) {
            throw new RuntimeException(sprintf(
                'رمز غير متوقّع عند الموضع %d في الصيغة: %s',
                $this->position,
                $formula
            ));
        }

        return $value;
    }

    /** جمع وطرح — أقل أسبقية */
    private function parseExpression(): float
    {
        $value = $this->parseTerm();

        while (true) {
            $this->skipWhitespace();
            $op = $this->peek();

            if ($op === '+') {
                $this->position++;
                $value += $this->parseTerm();
            } elseif ($op === '-') {
                $this->position++;
                $value -= $this->parseTerm();
            } else {
                return $value;
            }
        }
    }

    /** ضرب وقسمة وباقي — أسبقية أعلى */
    private function parseTerm(): float
    {
        $value = $this->parseFactor();

        while (true) {
            $this->skipWhitespace();
            $op = $this->peek();

            if ($op === '*') {
                $this->position++;
                $value *= $this->parseFactor();
            } elseif ($op === '/') {
                $this->position++;
                $divisor = $this->parseFactor();
                // القسمة على صفر تُرجع صفراً لا خطأ: الصيغ تشير غالباً إلى
                // قيم قد تكون صفرية (أيام عمل، ساعات) في شهر بلا بيانات.
                $value = $divisor === 0.0 ? 0.0 : $value / $divisor;
            } elseif ($op === '%') {
                $this->position++;
                $divisor = $this->parseFactor();
                $value = $divisor === 0.0 ? 0.0 : fmod($value, $divisor);
            } else {
                return $value;
            }
        }
    }

    /** رقم، متغيّر، قوس، أو سالب أحادي */
    private function parseFactor(): float
    {
        $this->skipWhitespace();
        $char = $this->peek();

        if ($char === null) {
            throw new RuntimeException('الصيغة تنتهي قبل تمامها');
        }

        if ($char === '+') {
            $this->position++;

            return $this->parseFactor();
        }

        if ($char === '-') {
            $this->position++;

            return -$this->parseFactor();
        }

        if ($char === '(') {
            $this->position++;
            $value = $this->parseExpression();
            $this->skipWhitespace();

            if ($this->peek() !== ')') {
                throw new RuntimeException('قوس غير مغلق في الصيغة');
            }

            $this->position++;

            return $value;
        }

        if (preg_match('/\G\d+(\.\d+)?/', $this->expression, $m, 0, $this->position)) {
            $this->position += strlen($m[0]);

            return (float) $m[0];
        }

        if (preg_match('/\G[A-Za-z_][A-Za-z0-9_]*/', $this->expression, $m, 0, $this->position)) {
            $this->position += strlen($m[0]);
            $name = $m[0];

            if (! array_key_exists($name, $this->variables)) {
                throw new RuntimeException(sprintf('متغيّر غير معروف في الصيغة: %s', $name));
            }

            return $this->variables[$name];
        }

        throw new RuntimeException(sprintf(
            'رمز غير مسموح "%s" عند الموضع %d',
            $char,
            $this->position
        ));
    }

    private function peek(): ?string
    {
        return $this->expression[$this->position] ?? null;
    }

    private function skipWhitespace(): void
    {
        while (($c = $this->peek()) !== null && ($c === ' ' || $c === "\t" || $c === "\n" || $c === "\r")) {
            $this->position++;
        }
    }
}

<?php

namespace Wc;

class Displayer
{
    private array $line = [];
    private string $output = '';

    public function display(): void
    {
        if (count($this->line) > 1) {
            $this->createEmptyTotalsLine();
            foreach ($this->line as $counts) {
                if (is_array($counts)) {
                    $this->getTotals($counts);
                }
            }
        }

        foreach ($this->line as $file => $counts) {
            if (is_array($counts)) {
                $this->output .= implode(
                        "  ",
                        array_filter([
                            $counts[Options::MODE_LINES->name] ?? null,
                            $counts[Options::MODE_WORDS->name] ?? null,
                            $counts[Options::MODE_CHARACTERS->name] ?? null,
                            $counts[Options::MODE_BYTES->name] ?? null,
                            $file ?? null
                        ])
                    ) . PHP_EOL;
            } else {
                $this->output .= $counts . PHP_EOL;
            }
        }

        echo $this->output;
    }

    public function addLine(string $file, array $counts): void
    {
        $this->line[$file] = $counts;
    }

    public function addExceptionLine(string $file, string $message): void
    {
        $this->line[$file] = "wc: $file: $message";
    }

    private function getTotals(array $counts): void
    {
        foreach ($counts as $key => $value) {
            $this->line['total'][$key] += $value;
        }
    }

    private function createEmptyTotalsLine(): void
    {
        $this->line['total'][Options::MODE_LINES->name] = 0;
        $this->line['total'][Options::MODE_WORDS->name] = 0;
        $this->line['total'][Options::MODE_CHARACTERS->name] = 0;
        $this->line['total'][Options::MODE_BYTES->name] = 0;
    }
}
<?php

namespace Wc;

    readonly class UserOptions
    {
        public function __construct(private array $modes, private array $files, private Options $readFrom = Options::FILES_MODE)
        {
        }

        public function getModes(): array
        {
            return empty($this->modes) ? $this->getDefaultModes() : $this->modes;
        }

        public function getFiles(): array
        {
            return empty($this->files) ? [STDIN] : $this->files;
        }

        private function getDefaultModes(): array
        {
            return [
                Options::MODE_LINES,
                Options::MODE_WORDS,
                Options::MODE_BYTES
            ];
        }

        public function getReadFrom(): Options
        {
            return $this->readFrom;
        }
    }
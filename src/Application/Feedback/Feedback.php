<?php
namespace conta\Application\Feedback;

use Monolog\Logger;

class Feedback
{
    private string $separator;

    /**
     * @var EntryCollection errors, warnings, and messages
     */
    private EntryCollection $feedback;

    public function __construct(string $separator = "\n")
    {
        $this->separator = $separator;
        $this->feedback = new EntryCollection();
    }

    public function getEntries(): EntryCollection
    {
        return $this->feedback;
    }

    public function getFeedback(): array
    {
        foreach ($this->feedback as $entry) {
            $feedback[] = $entry->getMessage();
        }
        return $feedback ?? [];
    }

    public function getFeedbackString(string $separator = null): string
    {
        $separator ??= $this->separator;
        $feedback = [];
        foreach ($this->feedback as $entry) {
            $feedback[] = $entry->getMessage();
        }
        return implode($separator, $feedback);
    }

    public function clearFeedback(): void
    {
        $this->feedback->clear();
    }

    public function addError(string $message, array $context = []): void
    {
        $this->addFeedback($message, Logger::ERROR, $context);
    }

    public function getErrors(): array
    {
        return $this->getSpecificFeedback(Logger::ERROR);
    }

    public function getErrorsString(string $separator = null): string
    {
        return $this->getSpecificFeedbackString(Logger::ERROR, $separator);
    }

    public function clearErrors(): void
    {
        $this->clearSpecificFeedback(Logger::ERROR);
    }

    public function addWarning(string $message, array $context = []): void
    {
        $this->addFeedback($message, Logger::WARNING, $context);
    }

    public function getWarnings(): array
    {
        return $this->getSpecificFeedback(Logger::WARNING);
    }

    public function getWarningsString(string $separator = null): string
    {
        return $this->getSpecificFeedbackString(
            Logger::WARNING,
            $separator
        );
    }

    public function clearWarnings(): void
    {
        $this->clearSpecificFeedback(Logger::WARNING);
    }

    public function addNotice(string $message, array $context = []): void
    {
        $this->addFeedback($message, Logger::NOTICE, $context);
    }

    public function getNotices(): array
    {
        return $this->getSpecificFeedback(Logger::NOTICE);
    }

    public function getNoticesString(string $separator = null): string
    {
        return $this->getSpecificFeedbackString(Logger::NOTICE, $separator);
    }

    public function clearNotices(): void
    {
        $this->clearSpecificFeedback(Logger::NOTICE);
    }

    public function addDebug(string $message, array $context = []): void
    {
        $this->addFeedback($message, Logger::DEBUG, $context);
    }

    public function getDebugs(): array
    {
        return $this->getSpecificFeedback(Logger::DEBUG);
    }

    public function getDebugsString(string $separator = null): string
    {
        return $this->getSpecificFeedbackString(
            Logger::WARNING,
            $separator
        );
    }

    public function clearDebugs(): void
    {
        $this->clearSpecificFeedback(Logger::DEBUG);
    }

    private function addFeedback(
        string $message,
        int $type,
        array $context = []
    ): void
    {
        $this->feedback->add(new Entry($message, $type, $context));
    }

    private function getSpecificFeedback(int $type): array
    {
        foreach ($this->feedback as $entry) {
            if ($entry->getType() !== $type) {
                continue;
            }
            $specific[] = $entry->getMessage();
        }
        return $specific ?? [];
    }

    private function getSpecificFeedbackString(
        string $type,
        ?string $separator
    ): string
    {
        $separator ??= $this->separator;
        return implode($separator, $this->getSpecificFeedback($type));
    }

    private function clearSpecificFeedback(int $type): void
    {
        $this->feedback->clear($type);
    }
}
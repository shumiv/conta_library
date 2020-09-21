<?php
namespace conta\Application\Request;

class Feedback
{
    const ERROR = 'errors';

    const WARNING = 'warnings';

    const MESSAGE = 'messages';

    private string $separator;

    /**
     * @var string[] errors, warnings, and messages
     */
    private array $feedback = [];

    /**
     * @var int[] feedback's indexes for errors
     */
    private array $errors = [];

    /**
     * @var int[] feedback's indexes for warnings
     */
    private array $warnings = [];

    /**
     * @var int[] feedback's indexes for messages
     */
    private array $messages = [];

    public function __construct(string $separator = "\n")
    {
        $this->separator = $separator;
    }

    public function getFeedback(): array
    {
        return $this->feedback;
    }

    public function getFeedbackString(string $separator = null): string
    {
        $separator ??= $this->separator;
        return implode($separator, $this->feedback);
    }

    public function clearFeedback(): void
    {
        $this->feedback = [];
    }

    public function addError(string $message): void
    {
        $this->addFeedback($message, self::ERROR);
    }

    public function getErrors(): array
    {
        return $this->getSpecificFeedback(self::ERROR);
    }

    public function getErrorsString(string $separator = null): string
    {
        return $this->getSpecificFeedbackString(self::ERROR, $separator);
    }

    public function clearErrors(): void
    {
        $this->clearSpecificFeedback(self::ERROR);
    }

    public function addWarning(string $message): void
    {
        $this->addFeedback($message, self::WARNING);
    }

    public function getWarnings(): array
    {
        return $this->getSpecificFeedback(self::WARNING);
    }

    public function getWarningsString(string $separator = null): string
    {
        return $this->getSpecificFeedbackString(self::WARNING, $separator);
    }

    public function clearWarnings(): void
    {
        $this->clearSpecificFeedback(self::WARNING);
    }

    public function addMessage(string $message): void
    {
        $this->addFeedback($message, self::MESSAGE);
    }

    public function getMessages(): array
    {
        return $this->getSpecificFeedback(self::MESSAGE);
    }

    public function getMessagesString(string $separator = null): string
    {
        return $this->getSpecificFeedbackString(self::MESSAGE, $separator);
    }

    public function clearMessages(): void
    {
        $this->clearSpecificFeedback(self::MESSAGE);
    }

    private function addFeedback(string $message, string $type): void
    {
        array_push($this->feedback, $message);
        $this->$type[] = array_key_last($this->feedback);
    }

    private function getSpecificFeedback(string $type): array
    {
        foreach ($this->$type as $index) {
            $specific[] = $this->feedback[$index];
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

    private function clearSpecificFeedback(string $type): void
    {
        foreach ($this->$type as $index) {
            unset($this->feedback[$index]);
        }
        $this->feedback = array_values($this->feedback);
    }
}
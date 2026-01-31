<?php

class Form
{
    private string $page;
    private string $prefix;
    private array $data;

    public function __construct(string $page, string $prefix, array $data)
    {
        $this->page = $page;
        $this->prefix = $prefix;
        $this->data = $data;
    }

    public function render(string $layout): string
    {
        foreach ($this->data as $field => $value) {
            $compValue = "";
            if (!is_array($value)) {
                $compValue = $value;
            }
            $layout = str_replace('[' . $this->prefix . $field . ']', $compValue, $layout);
            $layout = displayFormError($this->page, $this->prefix . $field, $layout);
        }
        return $layout;
    }

    public function displayErrors(string $layout): string
    {
        foreach ($this->data as $field => $value) {
            $layout = displayFormError($this->page, $field, $layout);
        }
        return $layout;
    }

    private function sessionKey(): string
    {
        return $this->page . ":form_data";
    }

    public function setValue($field, $value): void
    {
        $this->data[$field] = $value;
        $_SESSION[$this->sessionKey()][$field] = $value;
    }

    public function getValue($field): mixed
    {
        return $this->data[$field];
    }

    public function saveValues($input): void
    {
        foreach ($this->data as $field => $value) {
            if (isset($input[$field])) {
                $this->data[$field] = htmlspecialchars($input[$field]);
                $_SESSION[$this->sessionKey()][$field] = htmlspecialchars($input[$field]);
            }
        }
    }

    public function loadDataFromSession(): void
    {
        if (isset($_SESSION[$this->sessionKey()])) {
            foreach ($this->data as $field => $value) {
                if (isset($_SESSION[$this->sessionKey()][$field])) {
                    $this->data[$field] = $_SESSION[$this->sessionKey()][$field];
                }
            }
            $this->clearSession();
        }
    }

    public function clearSession(): void
    {
        unset($_SESSION[$this->sessionKey()]);
    }
}

?>
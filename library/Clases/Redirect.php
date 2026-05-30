<?php

namespace Clases;

final class Redirect {
    public static function to(string $url): void
    {
        header("Location: /library/$url");
        exit;
    }
}

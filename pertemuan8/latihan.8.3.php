```php
<?php

// Membuat fungsi repeat dengan parameter default 10
function repeatText($text, $num = 10)
{
    echo "<ol>";

    for ($i = 0; $i < $num; $i++) {
        echo "<li>" . $text . "</li>";
    }

    echo "</ol>";
}

// Memanggil fungsi dengan 2 argument
repeatText("I'm the best", 15);

// Memanggil fungsi dengan 1 argument
// Nilai $num otomatis menjadi 10
repeatText("You're the man");

?>
```

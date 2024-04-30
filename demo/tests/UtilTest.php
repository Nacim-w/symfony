<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use App\Entity\Article;

class UtilTest extends TestCase
{
    public function testSomething(): void
    {
        $this->assertTrue(true);
    }

    public function testTitleLength()
{
$title = 'Notre Produit';
$article = new Article($title);
$article->setTitle($title);
$this->assertGreaterThan(5, strlen($article->getTitle()));
}

public function testTitleIsString()
{
$title = 'Sample Title';
$article = new Article($title);
$article->setTitle($title);
$actualTitle = $article->getTitle();

$this->assertIsString($actualTitle);

}

public function testTitleStartsWithUpperCase()
{
$title = 'Sample Title';
$article = new Article($title);
$article->setTitle($title);
$this->assertMatchesRegularExpression('/^[A-Z]/', $article->getTitle());
}
}

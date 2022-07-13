<?php


if (is_readable($this->getPath('README.md'))) {
    [$readmeToc, $readmeContent] = rex_markdown::factory()->parseWithToc(rex_file::require($this->getPath('README.md')), 1, 2, [
        rex_markdown::SOFT_LINE_BREAKS => false,
        rex_markdown::HIGHLIGHT_PHP => true,
    ]);
    $fragment = new rex_fragment();
    $fragment->setVar('content', $readmeContent, false);
    $fragment->setVar('toc', $readmeToc, false);
    echo $fragment->parse('core/page/docs.php');
}
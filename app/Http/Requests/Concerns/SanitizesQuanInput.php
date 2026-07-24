<?php

namespace App\Http\Requests\Concerns;

use DOMDocument;
use DOMElement;
use DOMNode;

trait SanitizesQuanInput
{
    protected function sanitizeBasicString(mixed $value): mixed
    {
        if (!is_string($value)) {
            return $value;
        }

        return trim(strip_tags($value));
    }

    protected function sanitizeRichText(?string $html): ?string
    {
        if ($html === null) {
            return null;
        }

        $html = trim($html);

        if ($html === '') {
            return null;
        }

        $allowedTags = [
            'p', 'br', 'strong', 'b', 'em', 'i', 'u',
            'ul', 'ol', 'li', 'blockquote', 'h2', 'h3', 'h4', 'a',
        ];

        $allowedAttributes = [
            'a' => ['href', 'target', 'rel'],
        ];

        $dom = new DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="utf-8" ?><div>' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $root = $dom->getElementsByTagName('div')->item(0);
        if (!$root instanceof DOMElement) {
            return strip_tags($html);
        }

        $this->sanitizeNode($root, $allowedTags, $allowedAttributes);

        $cleanHtml = '';
        foreach (iterator_to_array($root->childNodes) as $childNode) {
            $cleanHtml .= $dom->saveHTML($childNode);
        }

        return trim($cleanHtml) ?: null;
    }

    protected function sanitizeNode(DOMNode $node, array $allowedTags, array $allowedAttributes): void
    {
        foreach (iterator_to_array($node->childNodes) as $childNode) {
            if ($childNode->nodeType === XML_COMMENT_NODE) {
                $node->removeChild($childNode);
                continue;
            }

            if ($childNode instanceof DOMElement) {
                $tagName = strtolower($childNode->tagName);

                if (!in_array($tagName, $allowedTags, true)) {
                    if (in_array($tagName, ['script', 'style'], true)) {
                        $node->removeChild($childNode);
                        continue;
                    }

                    while ($childNode->firstChild) {
                        $node->insertBefore($childNode->firstChild, $childNode);
                    }

                    $node->removeChild($childNode);
                    continue;
                }

                foreach (iterator_to_array($childNode->attributes) as $attribute) {
                    $attributeName = strtolower($attribute->nodeName);
                    $allowedForTag = $allowedAttributes[$tagName] ?? [];

                    if (!in_array($attributeName, $allowedForTag, true)) {
                        $childNode->removeAttribute($attributeName);
                        continue;
                    }

                    if ($tagName === 'a' && $attributeName === 'href') {
                        $href = trim($attribute->nodeValue);
                        $hasAllowedScheme = str_starts_with($href, 'http://')
                            || str_starts_with($href, 'https://')
                            || str_starts_with($href, 'mailto:')
                            || str_starts_with($href, 'tel:')
                            || str_starts_with($href, '#');

                        if (!$hasAllowedScheme) {
                            $childNode->removeAttribute('href');
                        }
                    }

                    if ($tagName === 'a' && $attributeName === 'target') {
                        if ($attribute->nodeValue !== '_blank') {
                            $childNode->removeAttribute('target');
                        } else {
                            $childNode->setAttribute('rel', 'noopener noreferrer nofollow');
                        }
                    }
                }

                $this->sanitizeNode($childNode, $allowedTags, $allowedAttributes);
            }
        }
    }
}

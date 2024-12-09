<?php
declare(strict_types=1);

namespace Salecto\DataIntegrity\Model;

use League\CommonMark\ConverterInterface;
use ReflectionClass;
use ReflectionException;

/**
 * Handles reading a markdown readme for a class
 * Assumes the readme has the same short name as the class with an .md extension
 * and exists in the same directory
 */
class ReadmeReader
{
    protected ConverterInterface $markdownConverter;

    public function __construct(
        ConverterInterface $markdownConverter
    )
    {
        $this->markdownConverter = $markdownConverter;
    }

    /**
     * Gets the readme in HTML format
     * @param string $className
     * @return string
     * @throws ReflectionException
     */
    public function getReadmeInHtml(string $className): string
    {
        $readmeHtml = '';
        $readmeFilePath = $this->getClassDirectoryPath($className) . DIRECTORY_SEPARATOR . $this->getClassFileName($className) . '.md';
        if (file_exists($readmeFilePath)) {
            $readmeRawContent = file_get_contents($readmeFilePath);
            if (is_string($readmeRawContent)) {
                $readmeHtml = $this->markdownConverter->convert($readmeRawContent)->getContent();
            }
        }

        return $readmeHtml;
    }

    /**
     * Gets the directory path of the class .
     * @param string $className
     * @return string
     * @throws ReflectionException
     */
    protected function getClassDirectoryPath(string $className): string
    {
        $reflection = new ReflectionClass($className);
        $filePath = $reflection->getFileName();
        return dirname($filePath);
    }

    /**
     * Gets the filename of the class without the namespace i.e shortname
     * @param string $className
     * @return string
     * @throws ReflectionException
     */
    protected function getClassFileName(string $className): string
    {
        return (new ReflectionClass($className))->getShortName();
    }
}

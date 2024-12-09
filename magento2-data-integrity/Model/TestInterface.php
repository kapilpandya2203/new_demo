<?php

namespace Salecto\DataIntegrity\Model;

interface TestInterface
{
    public function run();

    public function configure();

    public function setName($name);

    public function getName();

    public function setCode($code);

    public function getCode();

    public function setDescriptionTemplate($description);

    public function getDescriptionTemplate();

    public function getSolutions();

    public function addIssue($issueData, array $descriptionData);

    public function getReadmeInHtml();
}

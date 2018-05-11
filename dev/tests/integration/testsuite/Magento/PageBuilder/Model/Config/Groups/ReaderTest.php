<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\PageBuilder\Model\Config\Groups;

use Magento\TestFramework\Helper\Bootstrap;

class ReaderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\PageBuilder\Model\Config\Groups\Reader
     */
    private $model;

    /**
     * @var \Magento\PageBuilder\Model\Config\FileResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    private $fileResolverMock;

    protected function setUp()
    {
        $objectManager = Bootstrap::getObjectManager();

        $this->fileResolverMock = $this->createMock(
            \Magento\PageBuilder\Model\Config\FileResolver::class
        );

        $this->model = $objectManager->create(
            \Magento\PageBuilder\Model\Config\Groups\Reader::class,
            ['fileResolver' => $this->fileResolverMock]
        );
    }

    /**
     * @expectedException \Magento\Framework\Exception\LocalizedException
     */
    public function testPartial()
    {
        $file = file_get_contents(__DIR__ . '/../../../_files/content_type/groups3.xml');
        $this->fileResolverMock->expects($this->once())
            ->method('get')
            ->willReturn([$file]);
        $this->model->read('global');
    }

    public function testMergeCompleteAndPartial()
    {
        $fileList = [
            file_get_contents(__DIR__ . '/../../../_files/content_type/groups1.xml'),
            file_get_contents(__DIR__ . '/../../../_files/content_type/groups2.xml'),
            file_get_contents(__DIR__ . '/../../../_files/content_type/groups3.xml'),
        ];
        $this->fileResolverMock->expects($this->once())
            ->method('get')
            ->with('group.xml', 'global')
            ->willReturn($fileList);
        $expected = include __DIR__ . '/../../../_files/content_type/groups_expected_merged_array.php';
        $this->assertEquals($expected, $this->model->read('global'));
    }
}

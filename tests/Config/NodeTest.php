<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace ManagerTest\Config;

use Manager\Config\Node;
use Manager\Exception\MissingConfigException;
use Silex\Application;

/**
 * Tests for {@see \Manager\Config\Node}
 *
 * @author Jefersson Nathan <malukenho@phpse.net>
 *
 * @group  Unitary
 * @covers \Manager\Config\Node
 */
class NodeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getWrongConfiguration
     *
     * @param $wrongConfig
     */
    public function testCheckForTheConfigKey($wrongConfig)
    {
        /** @var Application|\PHPUnit_Framework_MockObject_MockObject $appMock */
        $appMock = $this->getMockBuilder(Application::class)
            ->disableOriginalConstructor()
            ->getMock();

        $appMock->expects(self::once())
            ->method('offsetExists')
            ->with('manager-config')
            ->willReturn(true);

        $appMock->expects(self::any())
            ->method('offsetGet')
            ->with('manager-config')
            ->willReturn($wrongConfig);

        $this->setExpectedException(MissingConfigException::class);
        new Node($appMock, 'dummy', 'index');
    }

    public function testCreateNodeWithOnlyRequiredConfigUsesTheDefaultValues()
    {
        /** @var Application|\PHPUnit_Framework_MockObject_MockObject $appMock */
        $appMock = $this->getMockBuilder('Silex\Application')
            ->disableOriginalConstructor()
            ->getMock();

        $appMock->expects(self::any())
            ->method('offsetExists')
            ->with('manager-config')
            ->will(
                self::returnValue([
                    'manager' => [

                    ],
                ]
                ));

        $appMock->expects(self::any())
            ->method('offsetGet')
            ->with('manager-config')
            ->will(
                self::returnValue([
                    'manager' => [
                        'dummy' => [
                            'index' => [
                                'columns' => []
                            ],
                        ],
                    ],
                ]));

        $node = new Node($appMock, 'dummy', 'index');
        self::assertSame(10, $node->getItemPerPage());
        self::assertSame([], $node->getColumns());
        self::assertSame('dummy', $node->getDbTable());
        self::assertSame('Manager: dummy', $node->getHeader());
        self::assertSame('setting', $node->getIcon());
        self::assertEmpty($node->getQuery());
        self::assertNull($node->getSearch());
    }

    public function getWrongConfiguration()
    {
        return [
            [
                [],
            ],
            [
                [
                    'manager' => [],
                ],
            ],
            [
                [
                    'manager' => [
                        'dummy' => [],
                    ],
                ],
            ],
            [
                [
                    'manager' => [
                        'dummy' => [
                            'index' => [],
                        ],
                    ],
                ],
            ],
        ];
    }
}

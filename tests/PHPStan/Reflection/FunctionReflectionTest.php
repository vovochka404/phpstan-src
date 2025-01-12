<?php declare(strict_types = 1);

namespace PHPStan\Reflection;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Testing\PHPStanTestCase;

class FunctionReflectionTest extends PHPStanTestCase
{

	public function dataPhpdocFunctions(): iterable
	{
		yield [
			'FunctionReflectionDocTest\\myFunction',
			'/** some fn phpdoc */',
		];
		yield [
			'FunctionReflectionDocTest\\noDocFunction',
			null,
		];
		yield [
			'FunctionReflectionDocTest\\docViaStub',
			'/** fn doc via stub */',
		];
		yield [
			'FunctionReflectionDocTest\\existingDocButStubOverridden',
			'/** fn stub overridden phpdoc */',
		];
		yield [
			'\\implode',
			'/** php-src native fn stub overridden phpdoc */',
		];
	}

	/**
	 * @dataProvider dataPhpdocFunctions
	 */
	public function testFunctionHasPhpdoc(string $functionName, ?string $expectedDoc): void
	{
		require_once __DIR__ . '/data/function-with-phpdoc.php';

		$reflectionProvider = $this->createReflectionProvider();

		$functionReflection = $reflectionProvider->getFunction(new Node\Name($functionName), null);
		$this->assertSame($expectedDoc, $functionReflection->getDocComment());
	}

	public function dataPhpdocMethods(): iterable
	{
		yield [
			'FunctionReflectionDocTest\\ClassWithPhpdoc',
			'__construct',
			'/** construct doc via stub */',
		];
		yield [
			'FunctionReflectionDocTest\\ClassWithPhpdoc',
			'aMethod',
			'/** some method phpdoc */',
		];
		yield [
			'FunctionReflectionDocTest\\ClassWithPhpdoc',
			'noDocMethod',
			null,
		];
		yield [
			'FunctionReflectionDocTest\\ClassWithPhpdoc',
			'docViaStub',
			'/** method doc via stub */',
		];
		yield [
			'FunctionReflectionDocTest\\ClassWithPhpdoc',
			'existingDocButStubOverridden',
			'/** stub overridden phpdoc */',
		];
		yield [
			'FunctionReflectionDocTest\\ClassWithInheritedPhpdoc',
			'aMethod',
			'/** some method phpdoc */',
		];
		yield [
			'FunctionReflectionDocTest\\ClassWithInheritedPhpdoc',
			'noDocMethod',
			null,
		];
		yield [
			'FunctionReflectionDocTest\\ClassWithInheritedPhpdoc',
			'docViaStub',
			'/** method doc via stub */',
		];
		yield [
			'FunctionReflectionDocTest\\ClassWithInheritedPhpdoc',
			'existingDocButStubOverridden',
			'/** stub overridden phpdoc */',
		];
		yield [
			'FunctionReflectionDocTest\\ClassWithInheritedPhpdoc',
			'aMethodInheritanceOverridden',
			'/** some inheritance overridden method phpdoc */',
		];
		yield [
			'\\DateTime',
			'__construct',
			'/** php-src native construct stub overridden phpdoc */',
		];
		yield [
			'\\DateTime',
			'modify',
			'/** php-src native method stub overridden phpdoc */',
		];
	}

	/**
	 * @dataProvider dataPhpdocMethods
	 */
	public function testMethodHasPhpdoc(string $className, string $methodName, ?string $expectedDocComment): void
	{
		$reflectionProvider = $this->createReflectionProvider();
		$class = $reflectionProvider->getClass($className);
		$scope = $this->createMock(Scope::class);
		$scope->method('isInClass')->willReturn(true);
		$scope->method('getClassReflection')->willReturn($class);
		$scope->method('canAccessProperty')->willReturn(true);
		$classReflection = $reflectionProvider->getClass($className);

		$methodReflection = $classReflection->getMethod($methodName, $scope);
		$this->assertSame($expectedDocComment, $methodReflection->getDocComment());
	}

	/**
	 * @return string[]
	 */
	public static function getAdditionalConfigFiles(): array
	{
		return [
			__DIR__ . '/data/function-reflection.neon',
		];
	}

}

<?php
namespace Sitegeist\Taxonomy\ElasticSearch\Eel;

use Neos\Flow\Annotations as Flow;
use Neos\ContentRepository\Domain\Model\NodeInterface;
use Sitegeist\Taxonomy\Service\TaxonomyService;
use Neos\Eel\ProtectedContextAwareInterface;

class TaxonomyIndexingHelper implements ProtectedContextAwareInterface
{

    /**
     * @var TaxonomyService
     * @Flow\Inject
     */
    protected $taxonomyService;

    /**
     * @param NodeInterface|NodeInterface[] $taxonomies
     * @return array
     */
    public function extractIdentifierAndParentIdentifiers($taxonomies)
    {

        if (!$taxonomies) {
            return [];
        }

        if ($taxonomies instanceof NodeInterface) {
            $taxonomies = [$taxonomies];
        }

        $identifiers = [];
        $taxonomyNodeType = $this->taxonomyService->getTaxonomyNodeType();

        foreach ($taxonomies as $taxonomy) {
            if (($taxonomy instanceof NodeInterface) && $taxonomy->getNodeType()->isOfType($taxonomyNodeType)) {
                $identifiers[] = $taxonomy->getIdentifier();
                $parent = $taxonomy->getParent();
                while ($parent && ($parent instanceof NodeInterface) && $parent->getNodeType()->isOfType($taxonomyNodeType)) {
                    $identifiers[] = $parent->getIdentifier();
                    $parent = $parent->getParent();
                }
            }
        }

        return array_unique($identifiers);
    }

    /**
     * @param string $methodName
     * @return bool
     */
    public function allowsCallOfMethod($methodName)
    {
        return true;
    }
}

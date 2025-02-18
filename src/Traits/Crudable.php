<?php

namespace Freelabois\LaravelQuickstart\Traits;

use Freelabois\LaravelQuickstart\Extendables\Resource;
use Freelabois\LaravelQuickstart\Interfaces\ManipulationManagerInterface;
use Freelabois\LaravelQuickstart\Interfaces\RepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

trait Crudable {
    use
        DoIndex,
        DoStore,
        DoUpdate,
        DoShow,
        DoDestroy;

    /**
     * @var ManipulationManagerInterface
     */
    protected $manager;
    /**
     * @var RepositoryInterface
     */
    protected $repository;


    /**
     * CrudController constructor.
     * @param ManipulationManagerInterface $manager
     * @param RepositoryInterface $repository
     */
    public function setup(
        ManipulationManagerInterface $manager,
        RepositoryInterface $repository
    ) {
        $this->manager = $manager;
        $this->repository = $repository;

        if (!isset($this->resource)) {
            $this->resource = Resource::class;
        }
    }


    public function iso8859toutf8($returnable) {
        $array = [];


        if ($returnable instanceof \Illuminate\Database\Eloquent\Model) {
            foreach ($returnable->getAttributes() as $att_key => $attribute_value) {
                $array[iconv("ISO-8859-1", "UTF-8", $att_key)] = $this->iso8859toutf8($attribute_value);
            }

            foreach ($returnable->getRelations() as $rel_key => $relationship) {
                $array[iconv("ISO-8859-1", "UTF-8", $rel_key)] = $this->iso8859toutf8($relationship);
            }
            return $array;
        }

        if (is_string($returnable)) {
            return iconv("ISO-8859-1", "UTF-8", $returnable);
        }

        if (is_bool($returnable) || is_numeric($returnable) || is_null($returnable)) {
            return $returnable;
        }

        if ($returnable instanceof LengthAwarePaginator) {


            $array['data'] = $this->iso8859toutf8($returnable->all());
            $array['total'] = $returnable->total();
            $array['lastPage'] = $returnable->lastPage();
            $array['perPage'] = $returnable->perPage();
            $array['currentPage'] = $returnable->currentPage();
            $array['path'] = $returnable->path();
            $array['next_page_url'] = $returnable->nextPageUrl();
            $array['prev_page_url'] = $returnable->previousPageUrl();
            $array['links'] = $returnable->linkCollection();

            return $array;
        }


        if (is_object($returnable) || is_array($returnable)) {

            foreach ($returnable as $key => $returnable_value) {

                if (is_numeric($key)) {
                    $array[] = $this->iso8859toutf8($returnable_value);
                    continue;
                }

                if (!in_array($key, ['timestamps', 'incrementing', 'preventsLazyLoading', 'exists',
                    'wasRecentlyCreated'])) {

                    $array[iconv("ISO-8859-1", "UTF-8", $key)] = $this->iso8859toutf8($returnable_value);

                }
            }


        }

        return $array;
    }


}

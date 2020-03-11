<?php

namespace App\Repositories;

use App\Ncov;

class NcovRepository
{
    /**
     * Store new ncov info in database
     *
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        return Ncov::create($data);
    }

    /**
     * Update ncov info in database
     *
     * @param Ncov $ncov
     * @param array $data
     * @return bool
     */
    public function update(Ncov $ncov, array $data)
    {
        $ncov->fill($data);

        return $ncov->save();
    }

    /**
     * Delete ncov info from database
     *
     * @param Ncov $ncov
     * @return bool|null
     * @throws \Exception
     */
    public function delete(Ncov $ncov)
    {
        return $ncov->delete();
    }

    /**
     * Get all ncov info
     *
     * @return Ncov[]|\Illuminate\Database\Eloquent\Collection
     */
    public function get()
    {
        return Ncov::all();
    }

    /**
     * Find ncov info by id
     *
     * @param $id
     * @return mixed
     */
    public function find($id)
    {
        return Ncov::find($id);
    }

    /**
     * Get last ncov info
     *
     * @return mixed
     */
    public function getLast()
    {
        return Ncov::orderBy('id', 'desc')->limit(1)->first();
    }

    public function getLatestForEachDay()
    {
        return Ncov::query()
            ->fromQuery('
                SELECT  n2.* 
                FROM ncovs n1 
                    LEFT JOIN ncovs n2 
                        ON n2.id=(
                            SELECT id 
                            FROM ncovs n3 
                            WHERE DATE(n3.created_at)=DATE(n1.created_at) 
                            ORDER BY created_at DESC 
                            LIMIT 1) 
                GROUP BY DATE(n1.created_at)');
    }
}

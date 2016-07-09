<?php

namespace App\Http\Controllers\Projects;

use App\Http\Requests\Features\CreateRequest;
use App\Http\Requests\Features\UpdateRequest;
use App\Http\Requests\Features\DeleteRequest;
use App\Http\Controllers\Controller;
use App\Entities\Projects\FeaturesRepository;

use Illuminate\Http\Request;

class FeaturesController extends Controller {

	private $repo;

	public function __construct(FeaturesRepository $repo)
	{
	    $this->repo = $repo;
	}

	public function create($projectId)
	{
		$project = $this->repo->requireProjectById($projectId);
		$workers = $this->repo->getWorkersList();
		return view('features.create',compact('project','workers'));
	}

	public function store(CreateRequest $req, $projectId)
	{
		$feature = $this->repo->createFeature($req->except('_token'), $projectId);
		flash()->success(trans('feature.created'));
		return redirect()->route('projects.features', $feature->project_id);
	}

	public function show($featureId)
	{
		$feature = $this->repo->requireById($featureId);
		return view('features.show', compact('feature'));
	}

	public function edit($featureId)
	{
		$feature = $this->repo->requireById($featureId);
		$workers = $this->repo->getWorkersList();
		return view('features.edit',compact('feature','workers'));
	}

	public function update(UpdateRequest $req, $featureId)
	{
		$feature = $this->repo->update($req->except(['_method','_token']), $featureId);
		flash()->success(trans('feature.updated'));
		return redirect()->route('projects.features', $feature->project_id);
	}

	public function delete($featureId)
	{
	    $feature = $this->repo->requireById($featureId);
		return view('features.delete', compact('feature'));
	}

	public function destroy(DeleteRequest $req, $featureId)
	{
	    $feature = $this->repo->requireById($featureId);
	    $projectId = $feature->project_id;
		if ($featureId == $req->get('feature_id'))
		{
			// $feature->tasks()->delete();
			$feature->delete();
	        flash()->success(trans('feature.deleted'));
		}
		else
			flash()->error(trans('feature.undeleted'));

		return redirect()->route('projects.features', $projectId);
	}

	public function tasks($featureId)
	{
	    $feature = $this->repo->requireById($featureId);
		return view('features.features', compact('feature'));
	}

}

<?php

namespace DTApi\Http\Controllers;

use DTApi\Models\Job;
use DTApi\Http\Requests;
use Illuminate\Http\Request;
use DTApi\Repository\BookingRepository;

/**
 * Class BookingController
 * @package DTApi\Http\Controllers
 */
class BookingController extends Controller
{

    /**
     * @var BookingRepository
     */
    protected $repository;

    /**
     * BookingController constructor.
     * @param BookingRepository $bookingRepository
     */
    public function __construct(BookingRepository $bookingRepository)
    {
        $this->repository = $bookingRepository;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        try {
            $response = [];
            if($request->has('user_id')) {
                $response = $this->repository->getUsersJobs($request->get('user_id'));
            }
            elseif($request->__authenticatedUser->user_type == env('ADMIN_ROLE_ID') || $request->__authenticatedUser->user_type == env('SUPERADMIN_ROLE_ID'))
            {
                $response = $this->repository->getAll($request);
            }
            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        try {
            $job = $this->repository->with('translatorJobRel.user')->find($id);
            return response()->json($job, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->all();

            $response = $this->repository->store($request->__authenticatedUser, $data);

            DB::commit();
            return response($response);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }

    }

    /**
     * @param $id
     * @param Request $request
     * @return mixed
     */
    public function update($id, Request $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->all();
            $cuser = $request->__authenticatedUser;
            $response = $this->repository->updateJob($id, array_except($data, ['_token', 'submit']), $cuser);

            DB::commit();
            return response($response);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function immediateJobEmail(Request $request)
    {
        try {
            $adminSenderEmail = config('app.adminemail');
            $data = $request->all();
            $response = $this->repository->storeJobEmail($data);

            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        } 
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getHistory(Request $request)
    {
        try {

            $response = [];
            if($request->has('user_id')) 
            {
                $response = $this->repository->getUsersJobsHistory($request->get('user_id'), $request);
            }

            return response()->json($response, 200);
        }
        catch(\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function acceptJob(Request $request)
    {
        try {
            $data = $request->all();
            $user = $request->__authenticatedUser;

            $response = $this->repository->acceptJob($data, $user);

            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function acceptJobWithId(Request $request)
    {
        try {
            $data = !empty($request->get('job_id')) ? $request->get('job_id') : null;
            $user = $request->__authenticatedUser;

            $response = $this->repository->acceptJobWithId($data, $user);

            return response()->json($response, 200);
        }
        catch(\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function cancelJob(Request $request)
    {
        try {
            $data = $request->all();
            $user = $request->__authenticatedUser;

            $response = $this->repository->cancelJobAjax($data, $user);

            return response()->json($response, 200);
        }
        catch(\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function endJob(Request $request)
    {
        try {
            $data = $request->all();

            $response = $this->repository->endJob($data);

            return response()->json($response, 200);
        }
        catch(\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }

    }

    public function customerNotCall(Request $request)
    {
        try {
            $data = $request->all();

            $response = $this->repository->customerNotCall($data);

            return response()->json($response, 200);
        }
        catch(\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }

    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getPotentialJobs(Request $request)
    {
        try {
            $data = $request->all();
            $user = $request->__authenticatedUser;

            $response = $this->repository->getPotentialJobs($user);

            return response()->json($response, 200);
        }
        catch(\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function distanceFeed(Request $request)
    {
        DB::beginTransaction();
        try {

            $data = $request->all();

            $response = $this->repository->distanceFeed($data)

            DB::commit();
            return response()->json($response, 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function reopen(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->all();
            $response = $this->repository->reopen($data);

            DB::commit();
            return response()->json($response, 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function resendNotifications(Request $request)
    {
        try {
            $data = $request->all();
            $jobid = !empty($data['jobid']) ? $data['jobid'] : null;
            $job = $this->repository->find($jobid);
            $job_data = $this->repository->jobToData($job);
            $this->repository->sendNotificationTranslator($job, $job_data, '*');

            return response(['success' => 'Push sent'], 200);
        }
        catch(\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        } 
    }

    /**
     * Sends SMS to Translator
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function resendSMSNotifications(Request $request)
    {
        try {
            $data = $request->all();
            $jobid = !empty($data['jobid']) ? $data['jobid'] : null;
            $job = $this->repository->find($jobid);
            $job_data = $this->repository->jobToData($job);
            $this->repository->sendSMSNotificationToTranslator($job);
            return response(['success' => 'SMS sent']);
        } catch (\Exception $e) {
            return response(['success' => $e->getMessage()]);
        }
    }

}

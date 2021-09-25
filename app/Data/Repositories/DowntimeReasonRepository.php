<?php


namespace App\Data\Repositories;

use App\Data\Models\DowntimeReason;
use App\Data\Repositories\Interfaces\PaginatedResultInterface;
use App\Data\Repositories\Interfaces\RawQueryBuilderOutputInterface;
use App\Data\Repositories\Traits\PaginatedOutputTrait;
use App\Data\Repositories\Traits\ProcessOutputTrait;
use App\Data\Repositories\Traits\RawQueryBuilderOutputTrait;

class DowntimeReasonRepository implements PaginatedResultInterface, RawQueryBuilderOutputInterface
{
    use ProcessOutputTrait, PaginatedOutputTrait, RawQueryBuilderOutputTrait;

    public function fetchAllDowntimeReasonsByGroup($downTimeReasonGroupId, $orderBy) {
        return DowntimeReason::where('reason_group_id', $downTimeReasonGroupId)->orderBy('name', $orderBy)->get();
    }

    public function storeDowntimeReason($request) {
        $downtimeReason = new DowntimeReason();
        $downtimeReason['reason_group_id'] = $request['reason_group_id'];
        $downtimeReason['name']             = $request['name'];
        $downtimeReason['type']             = $request['type'];
        $check = $downtimeReason->save();
        if ($check) {
            return true;
        }
        return false;
    }

    public function updateDowntimeReason($request, $id) {
        $downtimeReason = DowntimeReason::find($id);
        $downtimeReason['reason_group_id'] = $request['reason_group_id'];
        $downtimeReason['name']             = $request['name'];
        $downtimeReason['type']             = $request['type'];
        $check = $downtimeReason->save();
        if ($check) {
            return true;
        }
        return false;
    }

    public function deleteDowntimeReason($id) {
        $downtimeReason = DowntimeReason::find($id);
        $check = $downtimeReason->delete();
        if ($check) {
            return true;
        }
        return false;
    }

}

<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Models\Vote;
use App\Models\VoteType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VoteController extends Controller
{
    private function getVotable(string $type, int $id): Post|Comment
    {
        return match ($type) {
            'post' => Post::findOrFail($id),
            'comment' => Comment::findOrFail($id),
            default => throw new \InvalidArgumentException('Invalid votable type.'),
        };
    }

    private function getVoteTypeId(string $name): int
    {
        return VoteType::where('name', $name)->firstOrFail()->id;
    }

    public function upvote(Request $request, string $type, int $id): JsonResponse
    {
        $this->authorize('create', Vote::class);

        $votable = $this->getVotable($type, $id);
        $user = $request->user();
        $voteTypeId = $this->getVoteTypeId('up');

        $existingVote = Vote::where('votable_type', $votable::class)
            ->where('votable_id', $votable->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingVote) {
            if ($existingVote->vote_type_id === $voteTypeId) {
                $existingVote->delete();

                return response()->json(['message' => 'Vote removed.', 'score' => $votable->vote_score]);
            }

            $existingVote->update(['vote_type_id' => $voteTypeId]);
        } else {
            Vote::create([
                'user_id' => $user->id,
                'votable_type' => $votable::class,
                'votable_id' => $votable->id,
                'vote_type_id' => $voteTypeId,
            ]);
        }

        return response()->json(['message' => 'Upvoted.', 'score' => $votable->vote_score]);
    }

    public function downvote(Request $request, string $type, int $id): JsonResponse
    {
        $this->authorize('create', Vote::class);

        $votable = $this->getVotable($type, $id);
        $user = $request->user();
        $voteTypeId = $this->getVoteTypeId('down');

        $existingVote = Vote::where('votable_type', $votable::class)
            ->where('votable_id', $votable->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingVote) {
            if ($existingVote->vote_type_id === $voteTypeId) {
                $existingVote->delete();

                return response()->json(['message' => 'Vote removed.', 'score' => $votable->vote_score]);
            }

            $existingVote->update(['vote_type_id' => $voteTypeId]);
        } else {
            Vote::create([
                'user_id' => $user->id,
                'votable_type' => $votable::class,
                'votable_id' => $votable->id,
                'vote_type_id' => $voteTypeId,
            ]);
        }

        return response()->json(['message' => 'Downvoted.', 'score' => $votable->vote_score]);
    }

    public function removeVote(Request $request, string $type, int $id): JsonResponse
    {
        $votable = $this->getVotable($type, $id);
        $user = $request->user();

        $vote = Vote::where('votable_type', $votable::class)
            ->where('votable_id', $votable->id)
            ->where('user_id', $user->id)
            ->first();

        if ($vote) {
            $this->authorize('delete', $vote);
            $vote->delete();
        }

        return response()->json(['message' => 'Vote removed.', 'score' => $votable->vote_score]);
    }
}

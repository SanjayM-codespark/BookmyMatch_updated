<?php

namespace App\Models;

use CodeIgniter\Model;

class PostModel extends Model
{
    public function is_liked($user_id, $post_id)
    {
        return $this->db->table('likes')
            ->where('user_id', $user_id)
            ->where('post_id', $post_id)
            ->where('like',1)
            ->countAllResults() > 0;
    }

    public function add_like($user_id, $post_id)
    {
        $exist = $this->db->table('likes')
            ->where('user_id', $user_id)
            ->where('post_id', $post_id)
            ->get();
    
        if ($exist->getNumRows() == 0) {
            $data = [
                'user_id' => $user_id,
                'post_id' => $post_id,
                'like' => 1
            ];
            return $this->db->table('likes')->insert($data);
        } else {
            $row = $exist->getRow();
            $s = $row->like; 
    
            if ($s == 1) {
                return $this->db->table('likes')
                    ->where('user_id', $user_id)
                    ->where('post_id', $post_id)
                    ->update(['like' => 0]); // Unlike
            } else {
                return $this->db->table('likes')
                    ->where('user_id', $user_id)
                    ->where('post_id', $post_id)
                    ->update(['like' => 1]); // Like
            }
        }
    }
    


    public function add_comment($user_id, $post_id, $comment)
{
    $data = array(
        'user_id' => $user_id,
        'post_id' => $post_id,
        'comment' => $comment
    );

    $exist = $this->db->table('likes')
        ->where('likes.user_id', $user_id)
        ->where('likes.post_id', $post_id) 
        ->get();

        return $this->db->table('likes')->insert($data);
        
}
public function delete_comment($user_id, $post_id,$comment_id)
{
    $data = array(
        'comment' => ''
    );

    $updated = $this->db->table('likes')
        ->where('user_id', $user_id)
        ->where('post_id', $post_id)
        ->where('id', $comment_id)
        ->update($data);

    if ($updated) {
        return true;
    } else {
        return false; 
    }
}

    public function post_details() {
        $posts = $this->db->table('posts as p')
        ->select('p.post_id,p.post_user_id, p.post_title, p.post_content, p.post_images_json, p.post_videos_json, l.like,u.user_name, u.user_profile_img')       
         ->join('users as u', 'u.user_id = p.post_user_id', 'left')
        ->join('likes as l', 'l.post_id = p.post_id AND l.user_id = p.post_user_id', 'left')
        ->where('p.post_status',1)
        ->groupBy('p.post_id')
        ->get();     
    
        return $posts;
    }
    public function get_comment($post_id)
    {
        $comment = $this->db->table('likes')
        ->where('likes.post_id', $post_id)
        ->where('likes.comment !=', '') 
        ->where('likes.comment IS NOT NULL')
        ->select('likes.*, u.user_name, u.user_profile_img')
        ->join('users as u', 'u.user_id = likes.user_id', 'left')
        ->get();

   return $comment;
    }
    public function edit_comment($user_id, $post_id,$id,$comment)
    {
        $commentData = [
            'comment' => $comment,
        ];
    $comment1 = $this->db->table('likes')
        ->where('user_id', $user_id)
        ->where('post_id', $post_id)
        ->where('id', $id)
        ->update($commentData);

   return $comment1;
    }
    
}

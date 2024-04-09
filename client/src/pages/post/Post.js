import React, { useCallback, useContext, useEffect, useState } from 'react';
import { useParams } from 'react-router-dom';
import { toast, ToastContainer } from 'react-toastify';
import styles from './post.module.scss';

import { useHttp } from '../../hooks/http.hook';

import { Input } from '../../components/input';
import { Button } from '../../components/button';
import { AuthContext } from '../../context/AuthContext';

export const Post = () => {
  const { id } = useParams();
  const { token } = useContext(AuthContext);
  const { request, loading, error } = useHttp();
  const [ post, setPost ] = useState({});
  const [ form, setForm ] = useState(post);

  const getPost = useCallback(async (id) => {
    return await request(`/post/${ id }`);
  }, [ request ]);

  useEffect(() => {
    getPost(id).then(setPost);
  }, [ id, getPost ]);

  const onFormChange = (e) => {
    setForm({ ...post, [e.target.name]: e.target.value });
  }

  const onUpdate = async () => {
    await request(`/post/${ id }`, {
      method: 'PUT',
      body: form,
      headers: {
        Authorization: `Bearer ${ token }`
      }
    });
  }

  if (error) toast.error(error);
  if (loading) return <h1>Loading...</h1>;

  return (
    <div className={ styles.layout }>
      <h1>Update Post: { form?.title || post?.title }</h1>

      <Input type="text"
             placeholder="Title"
             name="title"
             value={ form?.title || post?.title }
             onChange={ onFormChange }/>

      <textarea rows="5"
                placeholder="Content"
                name="content"
                value={ form?.content || post?.content }
                onChange={ onFormChange }/>

      <Input type="text"
             placeholder="Author ID"
             name="author_id"
             value={ post?.author_id }
             disabled/>

      <Input type="text"
             placeholder="Status"
             name="status"
             value={ form?.status || post?.status }
             disabled/>

      <Button text="Update" onClick={ onUpdate }/>

      <ToastContainer/>
    </div>
  );
}

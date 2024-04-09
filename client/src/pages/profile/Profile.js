import React, { useCallback, useContext, useEffect, useState } from 'react';
import { toast, ToastContainer } from 'react-toastify';
import styles from './profile.module.scss';

import { useHttp } from '../../hooks/http.hook';

import { Input } from '../../components/input';
import { AuthContext } from '../../context/AuthContext';

export const Profile = () => {
  const { token } = useContext(AuthContext);
  const { request, loading, error } = useHttp();
  const [ user, setUser ] = useState({
    username: '',
    role: ''
  });

  const getMe = useCallback(async () => {
    return await request(`/user/me`, {
      headers: { Authorization: `Bearer ${ token }` }
    })
  }, [ token, request ]);

  useEffect(() => {
    getMe().then(setUser);
  }, [ getMe ]);

  if (error) toast.error(error);
  if (loading) return <h1>Loading...</h1>;

  return (
    <div className={ styles.layout }>
      <h1>User: { user?.username }</h1>

      <Input type="text"
             placeholder="Username"
             name="title"
             value={ user?.email } disabled/>

      <Input type="text"
             placeholder="Role"
             name="title"
             value={ user?.role } disabled/>

      <ToastContainer/>
    </div>
  );
}

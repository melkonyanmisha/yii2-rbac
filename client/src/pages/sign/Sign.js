import React, { useContext, useEffect, useState } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import { toast, ToastContainer } from 'react-toastify';
import styles from './sign.module.scss';

import { useHttp } from '../../hooks/http.hook';
import { AuthContext } from '../../context/AuthContext';

import { Input } from '../../components/input';
import { Button } from '../../components/button';

export const Sign = () => {
  const { sign } = useParams();
  const { login, isAuthenticated } = useContext(AuthContext);
  const navigate = useNavigate();
  const { request, loading, error } = useHttp();
  const [ form, setForm ] = useState({
    username: '',
    password: ''
  });
  const [ mode, setMode ] = useState({
    name: '',
    action: ''
  });

  useEffect(() => {
    if (isAuthenticated) return navigate('/');

    switch (sign) {
      case 'sign-in':
        return setMode({ name: 'Sign In', action: 'login' });
      case 'sign-up':
        return setMode({ name: 'Sign Up', action: 'register' });
      default:
        navigate('/')
    }
  }, [ sign, navigate, isAuthenticated ]);

  const onFormChange = (e) => {
    setForm({ ...form, [e.target.name]: e.target.value });
  }

  const onSubmit = async () => {
    if (!(form.username && form.password)) return;

    const data = await request(`/user/${ mode.action }`, {
      method: 'POST',
      body: form
    });
    if (data) {
      const user = await request(`/user/me`, {
        headers: { Authorization: `Bearer ${ data.access_token }` }
      });

      login(data.access_token, user.id);
      navigate('/');
    }
  }

  if (error) toast.error(error);
  if (loading) return <h1>Loading...</h1>;

  return (
    <div className={ styles.layout }>
      <div className={ styles.form }>
        <h1>{ mode.name }</h1>

        <Input type="text"
               placeholder="Username"
               name="username"
               value={ form.username }
               onChange={ onFormChange }/>

        { sign === 'sign-up' &&
          <Input type="email"
                 placeholder="Email"
                 name="email"
                 value={ form.email }
                 onChange={ onFormChange }/>
        }

        <Input type="password"
               placeholder="Password"
               name="password"
               value={ form.password }
               onChange={ onFormChange }/>

        <Button text={ mode.name } onClick={ onSubmit } disabled={ !(form.username && form.password) }/>
      </div>

      <ToastContainer/>
    </div>
  );
}

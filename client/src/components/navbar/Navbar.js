import React, { useCallback, useContext, useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { toast } from 'react-toastify';
import styles from './navbar.module.scss';

import { useHttp } from '../../hooks/http.hook';
import { AuthContext } from '../../context/AuthContext';

import { Button } from '../button';
import { Dropdown } from '../dropdown';

export const Navbar = () => {
  const navigate = useNavigate();
  const { request, loading, error } = useHttp();
  const { token, userId, isAuthenticated } = useContext(AuthContext);
  const [ user, setUser ] = useState(null);
  const [ show, setShow ] = useState(false);

  const getUser = useCallback(async () => {
    return await request(`/user/me`, {
      headers: { Authorization: `Bearer ${ token }` }
    })
  }, [ token, request ])

  useEffect(() => {
    isAuthenticated && getUser().then(setUser);
  }, [ isAuthenticated, getUser ]);

  if (error) toast.error(error);
  if (loading) return <h1>Loading...</h1>;

  return (
    <div className={ styles.layout }>
      <ul className={ styles.links }>
        { isAuthenticated
          ? (
            <>
              <li className={ styles.link }>
                <Button text={ user?.username ?? 'User' } onClick={ () => setShow(prevState => !prevState) }/>
                { show && <Dropdown user={ userId } setShow={ setShow }/> }
              </li>
            </>
          )
          : (
            <>
              <li className={ styles.link }>
                <Button text="Register" onClick={ () => navigate('/auth/sign-up') }/>
              </li>

              <li className={ styles.link }>
                <Button text="Login" onClick={ () => navigate('/auth/sign-in') }/>
              </li>
            </>
          )
        }
      </ul>
    </div>
  );
}

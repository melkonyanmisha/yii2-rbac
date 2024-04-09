import { useState, useCallback } from 'react';

const API_URL = process.env.REACT_APP_API_URL;

export const useHttp = () => {
  const [ error, setError ] = useState(null);
  const [ loading, setLoading ] = useState(false);

  const request = useCallback(async (url, init = {}) => {
    setLoading(true);

    if (!init.method) init.method = 'GET';
    if (!init.body) init.body = null;
    if (!init.headers) init.headers = {};

    if (init.body) {
      init.body = JSON.stringify(init.body);
      init.headers['Content-Type'] = 'application/json';
    }

    try {
      const response = await fetch(`${ API_URL }${ url }`, { ...init, credentials: 'include' });

      const data = await response.json();

      if (!response.ok) {
        throw new Error(data.message || 'Something went wrong...');
      }

      setLoading(false);

      return data;
    } catch (e) {
      setLoading(false);
      setError(e.message);
    }
  }, []);

  const clearError = useCallback(() => setError(null), []);

  return { loading, request, error, clearError };
}

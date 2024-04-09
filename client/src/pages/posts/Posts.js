import React, {useCallback, useContext, useEffect, useState, useTransition} from 'react';
import qs from 'qs';
import moment from 'moment';
import {toast, ToastContainer} from 'react-toastify';
import {useNavigate, useParams} from 'react-router-dom';
import styles from './posts.module.scss';

import {useHttp} from '../../hooks/http.hook';
import {AuthContext} from '../../context/AuthContext';

import {Input} from '../../components/input';
import {Button} from '../../components/button';

const initialState = {
    id: '',
    title: '',
    content: '',
    author_id: '',
    status: '',
    start_date: '',
    end_date: ''
}

export const Posts = () => {
    const {user} = useParams();
    const navigate = useNavigate();
    const {request, error} = useHttp();
    const {userId, isAuthenticated} = useContext(AuthContext);
    const [, startTransition] = useTransition();
    const [posts, setPosts] = useState([]);
    const [filter, setFilter] = useState(initialState);

    const getPosts = useCallback(async () => {
        const query = qs.stringify(filter, {
            filter: (prefix, value) => {
                if (user && prefix === 'author_id') return user;
                if (value && prefix === 'start_date') return moment(value).valueOf() / 1000;
                if (value && prefix === 'end_date') return moment(value).valueOf() / 1000;

                if (value) return value;
            }
        });

        return !query
            ? await request(`/post`)
            : await request(`/post?${query}`)
    }, [user, filter, request]);

    useEffect(() => {
        getPosts().then(setPosts);
    }, [getPosts]);

    const onSearch = (e) => {
        startTransition(() => {
            setFilter({...filter, [e.target.name]: e.target.value});
        });
    }

    if (error) toast.error(error);

    return (
        <div className={styles.layout}>
            <table className={styles.table}>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Content</th>
                    <th>Author</th>
                    <th>Status</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <tr className={styles.filters}>
                    <td>
                        <Input name="id" value={filter.id} onChange={onSearch}/>
                    </td>
                    <td>
                        <Input name="title" value={filter.title} onChange={onSearch}/>
                    </td>
                    <td>
                        <Input name="content" value={filter.content} onChange={onSearch}/>
                    </td>
                    <td>
                        <Input name="author_id" value={filter.author_id} onChange={onSearch} disabled={!!user}/>
                    </td>
                    <td>
                        <Input name="status" value={filter.status} onChange={onSearch}/>
                    </td>
                    <td>
                        <Input type="date" name="start_date" value={filter.start_date} onChange={onSearch}/>
                    </td>
                    <td>
                        <Input type="date" name="end_date" value={filter.end_date} onChange={onSearch}/>
                    </td>
                    <td>
                        <Button text="Reset" onClick={() => setFilter(initialState)}/>
                    </td>
                </tr>

                {posts.map((post) => (
                    <tr key={post.id}>
                        <td>{post.id}</td>
                        <td>{post.title}</td>
                        <td>{post.content}</td>
                        <td>{post.author_id}</td>
                        <td>{post.status}</td>
                        <td>{moment(post.created_at * 1000).format('YYYY-MM-DD HH:mm:ss')}</td>
                        <td>{moment(post.updated_at * 1000).format('YYYY-MM-DD HH:mm:ss')}</td>

                        {isAuthenticated && userId === post.author_id &&
                            <td>
                                <Button text="Edit" onClick={() => navigate(`/post/${post.id}`)}/>
                            </td>
                        }
                    </tr>
                ))}
                </tbody>
            </table>

            <ToastContainer/>
        </div>
    );
}
import React, { Component } from 'react';
import Axios from 'axios';


class Home extends Component {
    constructor() {
        super();

        this.state = {
            form: {},
            job: null,
            result: null,
        };
        this.intervalId = null;

        this.axios = Axios.create({
            baseURL: '/api/',
            timeout: 1000,
        });

        this.handleChange = this.handleChange.bind(this);
        this.handleClick = this.handleClick.bind(this);
        this.handleKeyPress = this.handleKeyPress.bind(this);

    }

    componentDidMount() {
        this.intervalId = setInterval(() => this.update(), 5000);
        this.update();
    }

    update() {
        if (this.state.job && (this.state.job.Status === 2 || this.state.job.Status === -1)) {
            this.axios.get('job/' + this.state.job.Id).then(
                (response) => {
                    const job = response.data;
                    this.setState({job: job});
                }
            ).catch(
                (error) => {
                    alert(error);
                }
            );
        }
        if (this.state.job && this.state.job.Status === 0) {
            const resultId = this.state.job.ResultId;
            if (resultId) {
                this.axios.get('result/' + resultId).then(
                    (response) => {
                        const result = response.data;
                        this.setState({result: result});
                    }
                ).catch(
                    (error) => {
                        alert(error);
                    }
                );
            }
        }
    }

    handleKeyPress(event) {
        if (event.key === 'Enter') {
            this.handleAction();
        }
    }

    handleChange(event) {
        let form = this.state.form;
        form[event.target.name] = event.target.value;
        this.setState({form: form});
    }

    handleClick(event) {
        switch (event.target.id) {
            case 'submit':
                this.handleAction();
                break;
            default:
                break;
        }
    }

    handleAction() {
        this.axios.post('job', {url: this.state.form.url})
            .then((response) => {
                this.setState({job: response.data, result: null});
            })
            .catch((error) => {
                alert(error);
            })
        ;
    }

    static renderJob(job) {
        if (!job) {
            return '';
        }
        return <Job key={job.Id} jobData={job}/>;
    }

    static renderResult(result) {
        if (!result) {
            return '';
        }
        return <Result key={result.Id} resultData={result}/>;
    }

    render() {
        const jobHtml = Home.renderJob(this.state.job);
        const resultHtml = Home.renderResult(this.state.result);
        return (
            <div>
                <div>
                    <fieldset className="fieldset">
                        <legend>Qrawler service</legend>
                        <p className="minussss">
                            Enter input URL:
                        </p>
                        <input
                            type="text"
                            placeholder="Input URL"
                            name="url"
                            onKeyPress={this.handleKeyPress}
                            onChange={this.handleChange}
                        />
                        <button id="submit" type="submit" onClick={this.handleClick} className="button small">
                            Validate
                        </button>
                    </fieldset>
                </div>
                <div>
                    {jobHtml}
                </div>
                <div>
                    {resultHtml}
                </div>
            </div>

        );
    }
}

function Job(props) {
    const job = props.jobData;
    let statusCaption;
    switch (job.Status) {
        case -1:
            statusCaption = 'Queued';
            break;
        case 0:
            statusCaption = 'Processed';
            break;
        case 1:
            statusCaption = 'Error: ' + job.Error;
            break;
        case 2:
            statusCaption = 'In progress';
            break;
        default:
            statusCaption = 'Unknown';
            break;
    }
    return (
        <div>
            <fieldset>
                <legend>Qrawler job</legend>
                <table>
                    <tbody>
                        <tr>
                            <th>Job ID</th>
                            <th>URL</th>
                            <th>Status</th>
                        </tr>
                        <tr>
                            <td>{job.Id}</td>
                            <td>{job.Input}</td>
                            <td>{statusCaption}</td>
                        </tr>
                    </tbody>
                </table>
            </fieldset>
        </div>
    );
}

function Result(props) {
    const result = props.resultData;
    const emailsDisplay = result.Emails.map((email, index) => <Email key={index} emailData={email} />);
    return (
        <div>
            <fieldset>
                <legend>Qrawler result</legend>
                <table>
                    <tbody>
                        <tr>
                            <th>Email</th>
                            <th>URL</th>
                        </tr>
                        {emailsDisplay}
                    </tbody>
                </table>
            </fieldset>
        </div>

    );
}

function Email(props) {
    const email = props.emailData;
    return (
        <tr>
            <td>{email.Email}</td>
            <td>{email.Url}</td>
        </tr>
    );
}

export default Home;

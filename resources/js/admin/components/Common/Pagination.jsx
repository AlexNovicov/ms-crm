import React from 'react';
import ReactPaginate from "react-paginate";

export default class Pagination extends React.Component {

    render() {

        if(this.props.countPages <= 1) {
            return null;
        }

        return <ReactPaginate
            previousLabel="&laquo;"
            nextLabel="&raquo;"
            breakLabel={'...'}
            breakClassName={'break-me'}
            pageCount={this.props.countPages}
            marginPagesDisplayed={2}
            pageRangeDisplayed={5}
            onPageChange={this.props.paginate}
            containerClassName={'pagination'}
            activeClassName={'active'}
            pageClassName={'page-item'}
            pageLinkClassName={'page-link'}
            previousClassName={'page-item'}
            nextClassName={'page-item'}
            previousLinkClassName={'page-link'}
            nextLinkClassName={'page-link'}
            forcePage={this.props.page ? this.props.page - 1 : 0}
        />;
    }
}
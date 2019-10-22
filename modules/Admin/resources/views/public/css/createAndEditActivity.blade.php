<style>
    .row_indent{
        margin-left: 0;
    }

    .vim-button-box {
        display: flex;
        flex-direction: row;
        justify-content: space-around;
        align-items: center;
        align-content: space-around;
    }

    .vim-button-box-item-activity-category {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        font-size: 45px;
        line-height: 1.22;
    }

    .activity-detail-category-item {
        margin: 2px 10px;
    }

    button.activity-detail-category-item {
        border-radius: 8px;
    }

    .form-group {
        display: flex;
        align-items: flex-start;
    }

    .vim-margin-top {
        margin-top: 10px;
    }

    .vim-margin-zero {
        margin: 0px;
    }

    .vim-flex-center-vertically {
        display: flex;
        justify-content: center;
        flex-direction: column;
        height: 40px;
    }

    .vim-panel {
        padding: 10px 15px;
        background-color: #f5f5f5;
        border: 1px solid #e2dede;
        border-radius: 4px;

    }

    .fa {
        cursor: pointer;
    }

    .padding-clear {
        padding: 0;
    }

    .padding-top-7 {
        padding-top: 7px;
    }

    .margin-top-5 {
        margin-top: 5px;
    }

    .margin-top-10{
        margin-top: 10px;
    }

    .activity-form {
        min-height: 310px;
        position: relative;
    }

    .activity-form > hr {
        margin-top: 20px;
        margin-bottom: 20px;
        border: 0;
        border-top: 1px solid #e8e6e6;
    }

    .activity-form-label {
        text-align: left;
    }



    @media screen and (min-width: 1000px) {
        .type-select {
            background: #f5f9fd;
            border: 1px solid #e5e6e7;
            width: 160px;
            height: 280px;
            position: absolute;
            left: 75%;
            z-index: 1000;
        }
    }

    @media screen and (min-width: 1500px) {
        .type-select {
            background: #f5f9fd;
            border: 1px solid #e5e6e7;
            width: 160px;
            height: 280px;
            position: absolute;
            left: 79%;
            z-index: 1000;
        }
    }

    .type-select ul li {
        list-style-type: none;
        background: #ffffff;
        border: 1px solid #e5e6e7;
        width: 63px;
        height: 24px;
        float: left;
        text-align: center;
        font-size: 12px;
        line-height: 20px;
        margin: 5px;
        cursor: pointer;
    }

    .type-select ul {
        margin: 0;
        padding: 0;
        margin-left: 6px;
    }

    .type-select > div {
        padding: 5px;
        padding-left: 17px;
        padding-top: 8px;
    }

    .is_necessary {
        text-align: right;
        margin-top: 7px;
    }

    .input-title {
        cursor: pointer;
    }
</style>


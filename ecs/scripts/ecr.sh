#!/bin/bash

# Variables
REGION="us-east-2"
ACCOUNT_ID="${AWS_ACCOUNT_ID}"
REPOSITORY_NAME="tickets-please"
IMAGE_NAME="tickets-please-app:latest"
ECR_URI="${ACCOUNT_ID}.dkr.ecr.${REGION}.amazonaws.com/${REPOSITORY_NAME}"

# Authenticate Docker to ECR
aws ecr get-login-password --region $REGION | docker login --username AWS --password-stdin $ECR_URI

# Tag the Docker image
docker tag $IMAGE_NAME $ECR_URI

# Push the Docker image to ECR
docker push $ECR_URI
